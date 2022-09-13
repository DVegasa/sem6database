<template>
  <div class="header">
    <router-link class="element" to="/list?t=mineral">Минералы</router-link>
    <router-link class="element" to="/list?t=shaft">Шахты</router-link>
    <router-link class="element" to="/list?t=supplier">Поставщики</router-link>
    <router-link class="element" to="/list?t=report">Отчёты</router-link>
    <router-link class="element" to="/list?t=shipment">Поставки</router-link>

    <div style="flex: 1" />

    <router-link class="element admin" to="/admin">Админ панель</router-link>
  </div>

  <div class="content">
    <router-view/>
  </div>
</template>

<style lang="scss">
@import "@/styles";

head, body {
  margin: 0;
  padding: 0;
}

.header {
  display: flex;
  align-content: center;
  gap: 10px;
  padding-left: 10px;
  padding-right: 10px;
  border-bottom: 1px solid #a7a7a7;

  .element {
    padding: 8px 10px;
    color: #000000;

    &:hover {
      background-color: #c6c6c6;
    }
  }
}

.content {
  padding: 10px;
}

</style>
