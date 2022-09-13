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
      </el-table>
    </div>
  </div>
</template>


<script setup>
import {computed, ref, watch} from "vue";
import {useRoute} from "vue-router"
import {useStore} from "@/features/useStore.js";

const route = useRoute()
const store = useStore()

const table = ref(null)
const entities = ref([])

watch([route, store.data, store.tables], ([route]) => {
  table.value = store.tables.value[route.query['t']]
  entities.value = store.data.value[route.query['t']]
}, {immediate: true})

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
          replacer = store.data.value['mineral'].find(e => e.id === entity[key])?.name ?? '(?)'
        }
        if (t === 'shaft') {
          replacer = store.data.value['shaft'].find(e => e.id === entity[key])?.name ?? '(?)'
        }
        if (t === 'supplier') {
          replacer = store.data.value['supplier'].find(e => e.id === entity[key])?.name ?? '(?)'
        }
        result[key] = replacer
      }
    }
    return result
  })
})



</script>


<style lang="scss" scoped>
@import "@/styles";

.pageList {

}
</style>
