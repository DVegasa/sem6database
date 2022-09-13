import {ref, watch} from "vue";
import axios from "axios";
import {useNotification} from "@/features/useNotification.js";

export const BackendErrorCodes = {
  REQUEST_FAILED: {
    code: 'REQUEST_FAILED', message: 'Запрос не удался на клиенте',
  },
  NO_RESPONSE: {
    code: 'NO_RESPONSE', message: 'Сервер не ответил на запрос',
  },
  RESPONSE_400: {
    code: 'RESPONSE_400', message: 'Ошибка в запросе',
  },
  RESPONSE_401: {
    code: 'RESPONSE_401', message: 'Вам нужно повторно авторизоваться',
  },
  RESPONSE_403: {
    code: 'RESPONSE_403', message: 'У Вас нет доступа',
  },
  RESPONSE_404: {
    code: 'RESPONSE_404', message: 'Не найдено',
  },
  RESPONSE_500: {
    code: 'RESPONSE_500', message: 'Ошибка на сервере',
  },
  RESPONSE_OTHER: {
    code: 'RESPONSE_OTHER', message: 'Ошибка неизвестного характера',
  },
}



const client = axios.create({
  method: 'POST',
  baseURL: process.env.VUE_APP_REST_ROOT || '',
})


export const useBackend = (options = {
  showNotificationOnError: true,
}) => {
  const loading = ref(false)

  const api = async (endpoint, payload, config) => {
    try {
      loading.value = true
      config = {
        data: payload,
        ...config,
      }
      return await client(endpoint, config)
    } catch {
      // повторная попытка, если сессия устарела
      try {
        return await client(endpoint, config)
      } catch (e) {
        if (e.response) {
          /* The request was made and the server responded with a status code that falls out of the range of 2xx */
          switch (e.response.status) {
            case 400: e.appCode = BackendErrorCodes.RESPONSE_400; break;
            case 401: e.appCode = BackendErrorCodes.RESPONSE_401; break;
            case 403: e.appCode = BackendErrorCodes.RESPONSE_403; break;
            case 404: e.appCode = BackendErrorCodes.RESPONSE_404; break;
            case 500: e.appCode = BackendErrorCodes.RESPONSE_500; break;
            default: e.appCode = BackendErrorCodes.RESPONSE_OTHER; break;
          }

        } else if (e.request) {
          /* The request was made but no response was received. `error.request` is an instance of XMLHttpRequest */
          e.appCode = BackendErrorCodes.NO_RESPONSE

        } else {
          /* Something happened in setting up the request that triggered an Error */
          e.appCode = BackendErrorCodes.REQUEST_FAILED;
        }

        if (options.showNotificationOnError) {
          useNotification().show({
            type: 'error',
            title: e.appCode.message,
            message: e.appCode.code,
          })
        }
        throw e
      }
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    api,
  }

}
