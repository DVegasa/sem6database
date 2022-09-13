<template>
  <div class="pageList">
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

const entities = ref([])
const table = ref(null)

const route = useRoute()
const store = useStore()
watch([route, store.data, store.tables], ([route]) => {
  entities.value = store.data.value[route.query['t']]
  table.value = store.tables.value[route.query['t']]
}, {immediate: true})

const tableData = computed(() => {
  return entities.value
})

const columns = computed(() => {
  if (!table.value) return []
  return Object.keys(table.value)
})

</script>


<style lang="scss" scoped>
@import "@/styles";

.pageList {

}
</style>
