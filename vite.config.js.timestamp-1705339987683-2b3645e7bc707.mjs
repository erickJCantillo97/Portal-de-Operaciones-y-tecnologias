// vite.config.js
import { defineConfig } from "file:///C:/Users/ecantillo/Documents/Software%20Projects/TOP/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/Users/ecantillo/Documents/Software%20Projects/TOP/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/Users/ecantillo/Documents/Software%20Projects/TOP/node_modules/@vitejs/plugin-vue/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: "resources/js/app.js",
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      },
      script: {
        defineModel: true,
        propsDestructure: true
      }
    })
  ]
  // resolve: {
  //     alias: {
  //         vue: path.resolve(
  //             "./node_modules/vue",
  //             "dist",
  //             "vue.runtime.esm-bundler.js"
  //         ),
  //     },
  // },
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxlY2FudGlsbG9cXFxcRG9jdW1lbnRzXFxcXFNvZnR3YXJlIFByb2plY3RzXFxcXFRPUFwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcZWNhbnRpbGxvXFxcXERvY3VtZW50c1xcXFxTb2Z0d2FyZSBQcm9qZWN0c1xcXFxUT1BcXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL2VjYW50aWxsby9Eb2N1bWVudHMvU29mdHdhcmUlMjBQcm9qZWN0cy9UT1Avdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tIFwidml0ZVwiO1xuaW1wb3J0IGxhcmF2ZWwgZnJvbSBcImxhcmF2ZWwtdml0ZS1wbHVnaW5cIjtcbmltcG9ydCB2dWUgZnJvbSBcIkB2aXRlanMvcGx1Z2luLXZ1ZVwiO1xuLy8gaW1wb3J0IHBhdGggZnJvbSAncGF0aCdcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIGxhcmF2ZWwoe1xuICAgICAgICAgICAgaW5wdXQ6IFwicmVzb3VyY2VzL2pzL2FwcC5qc1wiLFxuICAgICAgICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICAgICAgfSksXG4gICAgICAgIHZ1ZSh7XG4gICAgICAgICAgICB0ZW1wbGF0ZToge1xuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xuICAgICAgICAgICAgICAgICAgICBiYXNlOiBudWxsLFxuICAgICAgICAgICAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgc2NyaXB0OiB7XG4gICAgICAgICAgICAgICAgZGVmaW5lTW9kZWw6IHRydWUsXG4gICAgICAgICAgICAgICAgcHJvcHNEZXN0cnVjdHVyZTogdHJ1ZSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pLFxuICAgIF0sXG4gICAgLy8gcmVzb2x2ZToge1xuICAgIC8vICAgICBhbGlhczoge1xuICAgIC8vICAgICAgICAgdnVlOiBwYXRoLnJlc29sdmUoXG4gICAgLy8gICAgICAgICAgICAgXCIuL25vZGVfbW9kdWxlcy92dWVcIixcbiAgICAvLyAgICAgICAgICAgICBcImRpc3RcIixcbiAgICAvLyAgICAgICAgICAgICBcInZ1ZS5ydW50aW1lLmVzbS1idW5kbGVyLmpzXCJcbiAgICAvLyAgICAgICAgICksXG4gICAgLy8gICAgIH0sXG4gICAgLy8gfSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFzVixTQUFTLG9CQUFvQjtBQUNuWCxPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBR2hCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxNQUNQLFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxNQUNBLFVBQVU7QUFBQSxRQUNOLG9CQUFvQjtBQUFBLFVBQ2hCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ3JCO0FBQUEsTUFDSjtBQUFBLE1BQ0EsUUFBUTtBQUFBLFFBQ0osYUFBYTtBQUFBLFFBQ2Isa0JBQWtCO0FBQUEsTUFDdEI7QUFBQSxJQUNKLENBQUM7QUFBQSxFQUNMO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBVUosQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
