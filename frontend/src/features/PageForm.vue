<template>
  <div class="pageForm">

    <div class="form">
      <el-form label-width="200px" @keydown.enter.prevent>

        <template v-for="column in columns">
          <el-form-item :label="column" :prop="column" >

            <template v-if="table[column] === 'string'">
              <el-input v-model="form[column]"></el-input>
            </template>

            <template v-if="table[column] === 'int'">
              <div v-if="column === 'id'">{{form?.[column] ?? ''}}</div>
              <el-input-number
                  v-else
                  v-model="form[column]"
              ></el-input-number>
            </template>

            <template v-if="table[column] === 'float'">
              <el-input-number precision="2" v-model="form[column]"></el-input-number>
            </template>

            <template v-if="table[column] === 'date'">
              <el-date-picker v-model="form[column]"></el-date-picker>
            </template>

          </el-form-item>
        </template>

        <el-form-item>
          <el-button type="primary" @click="onSubmit">Сохранить</el-button>
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
    form[col] = entity.value?.[col] ?? ''
    if (table.value[col] === 'int' || table.value[col] === 'float') {
      form[col] = Number(form[col])
    }
  }
}, {immediate: true})


const onSubmit = async () => {
  await store.u(route.query['t'], form)
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
