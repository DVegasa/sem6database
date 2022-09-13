<template>
  <div class="pageAdmin">
    <h1>Админ панель</h1>
    <div class="sub">
      Чтобы воспользоваться данной панелью, вам необходимо ввести пароль администратора перед выполнением операции.
    </div>

    <div class="content">
      <div class="form">

        <el-form :model="form" label-width="120px" label-position="top" @keydown.enter.prevent>
          <el-form-item label="Запрос">
            <el-input v-model="form.command" type="textarea" :rows="6" style="font-family: monospace;"/>
          </el-form-item>

          <el-form-item label="Пароль администратора">
            <el-input v-model="form.password" type="password" />
          </el-form-item>

          <el-form-item>
            <div style="flex: 1;" />
            <el-button type="primary" @click="onExec">exec</el-button>
            <el-button type="primary" @click="onQuery">query</el-button>
          </el-form-item>
        </el-form>

      </div>

      <div class="result">
        <pre class="text">{{resultText}}</pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import {reactive, ref} from "vue";
import {useBackend} from "@/features/useBackend.js";

const form = reactive({
  password: '',
  command: '',
})

const resultText = ref()

const backend = useBackend()

const onQuery = async () => {
  const res = await backend.api('/query', {
    p: form.password,
    s: form.command,
  })
  resultText.value = res.data.result
}

const onExec = async () => {
  const res = await backend.api('/exec', {
    p: form.password,
    s: form.command,
  })
  resultText.value = res.data.result
}

</script>

<style lang="scss" scoped>
@import "@/styles";

.pageAdmin {
  min-height: 90vh;
}

.content {
  display: flex;
  height: 100%;
  gap: 10px;
  margin-top: 20px;
}

.form {
  flex: 1;
}

.result {
  flex: 1;
  height: 100%;
  background-color: #272727;
  color: white;
  min-height: 200px;
  padding: 10px;
  border-radius: 5px;

  .text {
    font-family: monospace;
  }
}
</style>
