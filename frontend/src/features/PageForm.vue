<template>
  <div class="pageForm">

    <h1 v-if="isCreateMode">Создание</h1>
    <h1 v-else>Обновление</h1>

    <div class="form">
      <el-form label-width="200px" @keydown.enter.prevent>

        <template v-for="column in columns">
          <el-form-item :label="column" :prop="column" >

            <template v-if="table[column] === 'string'">
              <el-input v-model="form[column]"></el-input>
            </template>

            <template v-if="table[column] === 'int'">
              <div v-if="column === 'id'">
                <template v-if="!isCreateMode">
                  {{form?.[column] ?? ''}}
                </template>
              </div>

              <el-select
                  v-if="column.startsWith('id_')"
                  v-model="form[column]"
              >
                <el-option
                    v-for="option in getOptionsForId(column)"
                    :label="option.label"
                    :value="option.value"
                ></el-option>
              </el-select>

              <el-input-number
                  v-if="column !== 'id' && !column.startsWith('id_')"
                  v-model="form[column]"
              ></el-input-number>
            </template>

            <template v-if="table[column] === 'float'">
              <el-input-number precision="2" v-model="form[column]"></el-input-number>
            </template>

            <template v-if="table[column] === 'date'">
              <el-date-picker v-model="form[column]" value-format="YYYY-MM-DD"></el-date-picker>
            </template>

          </el-form-item>
        </template>

        <el-form-item>
          <el-button type="primary" @click="onSubmit">
            <template v-if="isCreateMode">Создать</template>
            <template v-else>Обновить</template>
          </el-button>
        </el-form-item>

      </el-form>
    </div>

  </div>
</template>

<script setup>
import {computed, ref, watch, reactive} from "vue";
import {useRoute, useRouter} from "vue-router"
import {useStore} from "@/features/useStore.js";
import {useNotification} from "@/features/useNotification.js";

const router = useRouter()
const route = useRoute()
const store = useStore()

const form = reactive({})

const isCreateMode = computed(() => {
  return !route.query?.['id']
})


const table = computed(() => {
  if (!route.query?.['t']) return {}
  return table.value = store.tables.value?.[route.query['t']] ?? {}
})

const entity = computed(() => {
  if (!route.query?.['t']) return []
  return (store.data.value?.[route.query['t']] ?? []).find(e => e.id === route.query['id']) ?? {}
})

const columns = computed(() => {
  if (!table.value) return []
  return Object.keys(table.value)
})

watch([columns, entity], ([cols]) => {
  if (!cols) return
  for (const col of cols) {
    form[col] = entity.value?.[col] ?? null
    if (table.value[col] === 'int' || table.value[col] === 'float') {
      form[col] = Number(form[col]) ?? null
    }
  }
}, {immediate: true})

const getOptionsForId = (column) => {
  const table = column.replace('id_', '')
  return (store.data.value?.[table] ?? []).map(e => {
    return {
      value: e.id,
      label: e.name,
    }
  })
}

const onSubmit = async () => {
  if (isCreateMode.value) {
    await store.c(route.query['t'], form)
  } else {
    await store.u(route.query['t'], form)
  }
  useNotification().show({
    type: 'success',
    title: 'Успешно!',
  })
  router.push({
    path: '/list',
    query: {
      t: route.query['t']
    },
  })
}

</script>

<style lang="scss" scoped>
@import "@/styles";

.pageForm {

}

.form {
  max-width: 700px;
}
</style>
