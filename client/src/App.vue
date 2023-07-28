<template>
  <a-layout id="components-layout-demo-side" style="min-height: 100vh">
    <a-layout-sider v-model="collapsed" collapsible>
      <div class="logo_img">
        <img class="logo" src="./assets/r1_logo.png" style="background: none"/>
      </div>

      <a-menu theme="dark" v-model="selectedKeys" :default-selected-keys="defaultSelectedKeys" mode="inline"
              :forceSubMenuRender="true"
              >
          <a-menu-item v-for="item in simpleSidebarData" :key="item.key" >
            <router-link :to="item.link"></router-link>
            <a-icon :type="item.icon"  />
            <span>{{ item.text }}</span>
          </a-menu-item>
          <a-sub-menu v-for="item in subSidebarData" :key="item.key">
          <span slot="title">
            <a-icon :type="item.icon" />
            <span>{{ item.text }}</span>
          </span>
            <a-menu-item v-for="child in item.children" :key="child.key">
              <router-link :to="child.link"></router-link>
              {{ child.text }}
            </a-menu-item>
          </a-sub-menu>
      </a-menu>

    </a-layout-sider>
    <a-layout>
      <MyHeader></MyHeader>

      <a-layout-content style="margin: 10px 16px">
        <a-breadcrumb>
          <span>/&nbsp;&nbsp;</span>
          <a-breadcrumb-item v-for="item in breadcrumb" :key="item">
            {{ item }}
          </a-breadcrumb-item>
        </a-breadcrumb>

        <router-view></router-view>
      </a-layout-content>
      <a-layout-footer style="text-align: center">
        Ant Design ©2018 Created by Ant UED
      </a-layout-footer>
    </a-layout>
  </a-layout>
</template>
<script>
import MyHeader from "@/components/common/MyHeader";
export default {
  name: "App",
  components: {MyHeader},
  data() {
    return {
      collapsed: false,
      defaultSelectedKeys:['1'],
      breadcrumb:[],
      selectedKeys:[],
      simpleSidebarData: [
        {
          key: "1",
          icon: "code",
          text: "脚本",
          link:'/script'
        },
        {
          key: "2",
          icon: "bug",
          text: "调试",
          link:'/debug'
        },
      ],
      subSidebarData: [
        {
          key: "3",
          icon: "branches",
          text: "测试",
          children: [
            { key: "3-1", text: "数据库备份", link: "/backup"},
            { key: "3-2", text: "Newman", link: "/newman"}
          ]
        },
        {
          key: "4",
          icon: "codepen",
          text: "生成器",
          children: [
            { key: "4-1", text: "代码生成器", link: "/code-generator"},
            { key: "4-2", text: "Slate生成器", link: "/slate-generator" }
          ]
        }
      ]
    };
  },
  created() {
    this.selectedKeys = this.defaultSelectedKeys;
    this.breadcrumb = [this.simpleSidebarData[0].text]
  },
  methods: {
    findTextByKey(key) {
      let result = [];
      // 在simpleSidebarData中查找
      console.log(key)
      for (let item of this.simpleSidebarData) {
        if (item.key === key) {
          result.push(item.text);
          break;
        }
      }
      // 在subSidebarData中查找
      for (let item of this.subSidebarData) {
        if (item.key === key) {
          result.push(item.text);
          break;
        }
        if (item.children && item.children.length > 0) {
          for (let child of item.children) {
            if (child.key === key) {
              result.push(item.text);
              result.push(child.text);
              break;
            }
          }
        }
      }
      return result;
    }
  },
  watch: {
    selectedKeys: {
      handler(newVal) {
        this.breadcrumb = this.findTextByKey(newVal[0]);
      },
      deep: true
    }
  },

};
</script>

<style>
#components-layout-demo-side .logo {
  height: 32px;
  background: rgba(255, 255, 255, 0.2);
  margin: 16px;
}
.logo_img{
  display: flex !important;
  justify-content: center!important;

}
</style>
