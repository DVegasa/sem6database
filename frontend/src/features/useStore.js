import {ref} from "vue";
import {useBackend} from "@/features/useBackend.js";

const data = ref({})
const tables = ref({})

const backend = useBackend()

const reload = async (tableName) => {
  if (tableName) {
    const res = await backend.api('/x', {
      t: tableName,
      a: 'l',
    })
    data.value[tableName] = res.data.result

  } else {
    const res = await backend.api('/tables')
    tables.value = res.data.result

    for (const table in tables.value) {
      const res = await backend.api('/x', {
        t: table,
        a: 'l',
      })
      data.value[table] = res.data.result
    }
  }

}

const c = async (table, payload) => {
  await backend.api('/x', {
    t: table,
    a: 'c',
    v: payload,
  })
  await reload(table)
}

const d = async (table) => {
  await backend.api('/x', {
    t: table,
    a: 'd',
  })
  await reload(table)
}

const u = async (table, payload) => {
  await backend.api('/x', {
    t: table,
    a: 'u',
    v: payload,
  })
  await reload(table)
}


Promise.resolve().then(async () => {
  await reload()
})

export const useStore = () => {
  return {
    reload,
    data,
    tables,
  }
}
