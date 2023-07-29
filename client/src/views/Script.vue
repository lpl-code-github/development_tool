<template>
  <div class="my-script">
    <div class="my-s-button">
      <a-input-search placeholder="输入脚本名或描述搜索" style="width: 200px" @search="onSearch"/>
      <a-button type="primary">
        添加
      </a-button>
    </div>
    <div class="my-s-table">
      <a-table :columns="columns" :data-source="tableData" @change="handleChange" :pagination="false">
        <span slot="tags" slot-scope="tags">
          <a-tag
              v-for="(t,index) in tags"
              :key="index"
              :color="t.color"
          >
            {{ t.text }}
          </a-tag>
        </span>
        <a slot="action" slot-scope="text" href="javascript:;">删除</a>
        <p slot="expandedRowRender" slot-scope="record" style="margin: 0">
          {{ record.description }}
        </p>
      </a-table>
    </div>
  </div>
</template>

<script>
export default {
  name: "Script",

  data() {
    return {
      columns: [
        {title: '名称', dataIndex: 'name', key: 'name'},
        {
          title: '标签',
          key: 'tags',
          dataIndex: 'tags',
          filters: [],
          scopedSlots: {customRender: 'tags'},
        },
        {title: '路径', dataIndex: 'path', key: 'age'},
        {title: '创建时间', dataIndex: 'created_at', key: 'address'},
        {title: 'Action', dataIndex: '', key: 'x', scopedSlots: {customRender: 'action'}},
      ],
      scriptData: [],
      tableData: [],
      filteredTags: [],
      tagsFilterDropdownVisible: false,
    }
  },
  created() {
    this.getScriptData()
  },
  methods: {
    onSearch(value) {
      console.log(value);
    },
    handleChange(pagination, filters) {
      var tagFilterChecked = filters.tags
      if (filters.tags.length === 0){
        this.tableData = this.scriptData
        return
      }
      this.tableData = this.scriptData.filter(item => {
        return item.tags.some(tag => tagFilterChecked.includes(tag.text));
      })
    },
    getScriptData() {
      this.scriptData = [
        {
          id: 1,
          name: 'John Brown',
          path: 32,
          tags: [
            {
              color: "#108ee9",
              text: "db"
            },
            {
              color: "#690c3d",
              text: "test"
            }
          ],
          created_at: 'New York No. 1 Lake Park',
          description: 'My name is John Brown, I am 32 years old, living in New York No. 1 Lake Park.',
        },
        {
          id: 2,
          name: 'Jim Green',
          path: 42,
          tags: [
            {
              color: "#690c3d",
              text: "test"
            }
          ],
          created_at: 'London No. 1 Lake Park',
          description: 'My name is Jim Green, I am 42 years old, living in London No. 1 Lake Park.',
        },
        {
          id: 3,
          name: 'Joe Black',
          path: 32,
          tags: [
            {
              color: "#3a9f75",
              text: "env"
            }
          ],
          created_at: 'Sidney No. 1 Lake Park',
          description: 'My name is Joe Black, I am 32 years old, living in Sidney No. 1 Lake Park.',
        },
        {
          id: 4,
          name: 'Joe Black',
          path: 32,
          tags: [
            {
              color: "#3a9f75",
              text: "env"
            }
          ],
          created_at: 'Sidney No. 1 Lake Park',
          description: 'My name is Joe Black, I am 32 years old, living in Sidney No. 1 Lake Park.',
        }
      ]
      const tagsFilters = [];

      this.scriptData.forEach(item => {
        item.tags.forEach(tag => {
          const existingTag = tagsFilters.find(filter => filter.text === tag.text);
          if (!existingTag) {
            tagsFilters.push({text: tag.text, value: tag.text});
          }
        });
      });

      this.columns.forEach(item => {
        if (item.key === 'tags') {
          item.filters = tagsFilters
        }
      })
      this.tableData = this.scriptData
    }
  }
}
</script>

<style scoped>
.my-script {

}

.my-s-button {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
}

.my-s-table {
  margin-top: 10px;
}
</style>
