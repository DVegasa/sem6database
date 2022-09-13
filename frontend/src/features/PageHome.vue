<template>
  <div class="pageHome">
    pageHome
  </div>
</template>

<script setup>
import {useNotification} from "@/features/useNotification.js";

useNotification().show({
  type: 'success',
  title: 'meow',
})
</script>

<style lang="scss" scoped>
@import "@/styles";

.pageHome {

}
</style>
