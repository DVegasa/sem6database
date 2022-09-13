<template>
  <div class="pageHome" @click="launch">
    pageHome
  </div>
</template>

<script setup>
import {useNotification} from "@/features/useNotification.js";
import {useBackend} from "@/features/useBackend.js";

const launch = async () => {
  useBackend().api('ping', {meow: 42})
}
</script>

<style lang="scss" scoped>
@import "@/styles";

.pageHome {

}
</style>
