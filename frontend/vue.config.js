const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  devServer: {
    allowedHosts: 'all',
    historyApiFallback: true,
    proxy: {
      '^/rest/': {
        target: process.env.VUE_APP_PROXY,
        changeOrigin: true,
        cookieDomainRewrite: 'localhost',
      },
    },
  },
})
