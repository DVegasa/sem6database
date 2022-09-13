<template>
  <div class="pageList">
    {{table}}
    <br />
    {{entities}}
  </div>
</template>

<script setup>
import {ref, watch} from "vue";
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

</script>

<style lang="scss" scoped>
@import "@/styles";

.pageList {

}
</style>
