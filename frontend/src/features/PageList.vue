<template>
  <div class="pageList">
    <el-checkbox v-model="showRawId" label="Показывать ID" />
    <div class="table">
      <el-table :data="tableData">
        <el-table-column
          v-for="col in columns"
          :prop="col"
          :label="col"
        />

        <el-table-column>
          <template #default="scope">
            <div style="display: flex">
              <el-button size="small" type="default" @click="onEdit(scope.row)">Редактировать</el-button>
              <el-button size="small" type="danger" @click="onDelete(scope.row)">Удалить</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>


<script setup>
import {computed, ref, watch} from "vue";
import {useRoute, useRouter} from "vue-router"
import {useStore} from "@/features/useStore.js";
import {useNotification} from "@/features/useNotification.js";

const router = useRouter()
const route = useRoute()
const store = useStore()

const table = computed(() => {
  if (!route.query?.['t']) return {}
  return table.value = store.tables.value?.[route.query['t']] ?? {}
})

const entities = computed(() => {
  if (!route.query?.['t']) return []
  return store.data.value?.[route.query['t']] ?? []
})


const columns = computed(() => {
  if (!table.value) return []
  return Object.keys(table.value)
})

const showRawId = ref(false)
const tableData = computed(() => {
  if (!entities.value) return []
  return entities.value.map(entity => {
    const result = {}
    for (const key in entity) {
      result[key] = entity[key]

      if (key?.startsWith('id_') && !showRawId.value) {
        const t = key.slice(3)
        let replacer = '(?)';
        if (t === 'mineral') {
          replacer = store.data.value?.['mineral']?.find(e => e.id === entity[key])?.name ?? '(?)'
        }
        if (t === 'shaft') {
          replacer = store.data.value?.['shaft']?.find(e => e.id === entity[key])?.name ?? '(?)'
        }
        if (t === 'supplier') {
          replacer = store.data.value?.['supplier']?.find(e => e.id === entity[key])?.name ?? '(?)'
        }
        result[key] = replacer
      }
    }
    return result
  })
})


const onDelete = async (entity) => {
  console.log('meow!')
  await store.d(route.query['t'], entity.id)
  useNotification().show({
    type: 'info',
    title: 'Запись успешно удалена',
  })
}

const onEdit = async (entity) => {
  router.push({
    path: '/form',
    query: {
      t: route.query['t'],
      id: entity.id,
    },
  })
}


</script>


<style lang="scss" scoped>
@import "@/styles";

.pageList {

}
</style>
