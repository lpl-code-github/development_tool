(self["webpackChunkclient"]=self["webpackChunkclient"]||[]).push([[517],{6517:function(t,e,a){"use strict";a.r(e),a.d(e,{default:function(){return D}});var s=function(){var t=this,e=t._self._c;return e("div",{staticClass:"my-script"},[e("AddScript",{attrs:{"open-flag":t.openAddScriptModel,"filter-option":t.allTags},on:{updateModelStatus:t.getAddScriptModelStatus,submit:t.updateTable}}),e("AddTag",{attrs:{"open-flag":t.openAddTagModel},on:{updateModelStatus:t.getAddTagModelStatus,submit:t.updateTags}}),e("div",{staticClass:"my-s-button"},[e("a-input-search",{staticStyle:{width:"200px"},attrs:{placeholder:"输入脚本名或描述搜索"},on:{search:t.onSearch}}),e("a-button",{attrs:{type:"primary"},on:{click:t.addScript}},[t._v(" 添加 ")])],1),e("div",{staticClass:"my-s-table"},[e("a-table",{attrs:{columns:t.columns,"data-source":t.tableData,expandedRowKeys:t.expandedRowKeys,pagination:t.paginationConfig},on:{expandedRowsChange:t.handleExpandedRowsChange,change:t.handleChange},scopedSlots:t._u([t._l(["name","description"],(function(a){return{key:a,fn:function(s,i,r){return[e("div",{key:a},[i.editable?e("a-input",{staticStyle:{margin:"-5px 0"},attrs:{type:"description"===a?"textarea":"",rows:"1",value:s},on:{change:e=>t.handleChangeEdit(e.target.value,i.key,a)}}):[t._v(" "+t._s(""===s||null===s?"/":s)+" ")]],2)]}}})),{key:"tags",fn:function(a,s){return e("span",{},[t._l(a,(function(a,i){return e("a-tag",{key:i,attrs:{closable:s.editable,color:a.color},on:{close:function(e){return t.removeTagItem(a.id,s.key,e)}}},[t._v(" "+t._s(a.name)+" ")])})),s.editable?e("a-select",{staticStyle:{width:"100px",border:"1px"},attrs:{size:"small","show-search":"","option-filter-prop":"children","filter-option":t.filterOption,dropdownMatchSelectWidth:!1,value:t.selectedTagsItems},on:{select:t.handleEditModeSelectTag},scopedSlots:t._u([{key:"dropdownRender",fn:function(a){return e("div",{},[e("v-nodes",{attrs:{vnodes:a}}),e("a-divider",{staticStyle:{margin:"4px 0"}}),e("div",{staticStyle:{padding:"4px 8px",cursor:"pointer"},on:{mousedown:t=>t.preventDefault(),click:t.addTags}},[e("a-icon",{attrs:{type:"plus"}}),t._v(" 添加新的标签 ")],1)],1)}}],null,!0)},t._l(t.tmpTags,(function(a,s){return e("a-select-option",{key:s,attrs:{value:a.id}},[e("div",{staticClass:"color-box",style:{"background-color":a.color}}),t._v(" "+t._s(a.name)+" ")])})),1):t._e(),null===a||0!==a.length||s.editable?t._e():e("span",[t._v("/")])],2)}},{key:"action",fn:function(a,s,i){return[e("div",{staticClass:"editable-row-operations"},[s.editable?e("span",[e("a",{staticStyle:{color:"#5f92ef"},on:{click:()=>t.save(s.key)}},[t._v("更新")]),e("a-divider",{staticStyle:{"background-color":"#a8a7a7!important"},attrs:{type:"vertical"}}),e("a-popconfirm",{attrs:{title:"确定取消吗？"},on:{confirm:()=>t.cancel(s.key)}},[e("a",{staticStyle:{color:"#5f6062"}},[t._v("取消")])])],1):e("span",[e("a",{staticStyle:{color:"#5f92ef"},attrs:{disabled:""!==t.editingKey},on:{click:()=>t.edit(s.key)}},[t._v("编辑")]),e("a-divider",{staticStyle:{"background-color":"#a8a7a7!important"},attrs:{type:"vertical"}}),e("a",{staticStyle:{color:"#2c982c"},on:{click:()=>t.runScript(s.key)}},[t._v("运行")]),e("a-divider",{staticStyle:{"background-color":"#a8a7a7!important"},attrs:{type:"vertical"}}),e("a",{staticStyle:{color:"#f17878"},on:{click:function(e){return t.deleteScript(s.key)}}},[t._v("删除")])],1)])]}},{key:"expandedRowRender",fn:function(a){return e("div",{staticStyle:{margin:"0"}},[e("span",[e("span",{staticStyle:{"font-weight":"bolder"}},[t._v("脚本文件路径：")]),t._v("   "),a.editable?e("span",[t._v(t._s(a.path))]):e("span",[e("a",{on:{click:function(e){return t.downloadScriptFile(a.path)}}},[t._v(t._s(a.path))])]),t._v("    "),a.editable?e("a-popconfirm",{attrs:{title:"确认重新上传脚本文件?","ok-text":"确认","cancel-text":"不了"},on:{confirm:t.confirmUpload}},[e("a",{staticStyle:{color:"#04628a"}},[t._v("重新上传")]),e("a-upload",{ref:"uploadComponent",attrs:{directory:!1,name:"file",multiple:!1,accept:".sh","before-upload":t.beforeUpload,"custom-request":()=>t.uploadUpdateScriptFileAndOther(a.key),"file-list":t.fileList}})],1):t._e()],1),e("br"),null!=a.properties?e("div",{staticStyle:{"margin-top":"15px"}},[e("a-list",{staticStyle:{width:"95%"},attrs:{size:"small",bordered:"","data-source":a.properties},scopedSlots:t._u([{key:"renderItem",fn:function(s,i){return e("a-list-item",{},[a.editable?e("div",[e("a-input",{staticStyle:{width:"400px"},attrs:{value:s},on:{change:e=>t.handleChangeEdit(e.target.value,a.key,"properties",i)}}),e("a-icon",{staticStyle:{display:"inline-grid","margin-left":"20px"},attrs:{type:"minus-circle-o"},on:{click:function(e){return t.removePropertiesItems(a.key,i)}}})],1):e("span",[t._v(t._s(s)+" ")])])}}],null,!0)},[e("div",{attrs:{slot:"header"},slot:"header"},[a.editable?e("span",[e("span",{staticStyle:{"font-weight":"bolder"}},[t._v("命令行参数")]),e("a-icon",{staticStyle:{"margin-left":"20px"},attrs:{type:"plus-circle-o"},on:{click:function(e){return t.addPropertiesItems(a.key)}}})],1):e("span",{staticStyle:{"font-weight":"bolder"}},[t._v("命令行参数")])])])],1):t._e()])}}],null,!0)})],1)],1)},i=[],r=(a(57658),function(){var t=this,e=t._self._c;return e("div",[e("a-modal",{attrs:{title:"添加一个新的脚本","on-ok":t.handleOk,afterClose:t.afterClose,maskClosable:!1},model:{value:t.visible,callback:function(e){t.visible=e},expression:"visible"}},[e("template",{slot:"footer"},[e("a-button",{key:"back",on:{click:t.handleCancel}},[t._v(" 取消 ")]),e("a-button",{key:"submit",attrs:{type:"primary",loading:t.loading},on:{click:t.handleOk}},[t._v(" 添加 ")])],1),e("a-form",{ref:"mainModal",staticStyle:{width:"100%"},attrs:{form:t.form,"label-col":{span:3},"wrapper-col":{span:20}}},[e("a-form-item",{attrs:{label:"名称",prop:"name"}},[e("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["name",{rules:[{required:!0,message:"Please input name!"},{min:5,max:50,message:"长度必须在 5 ～ 50",trigger:"blur"}]}],expression:"['name', { rules: [{ required: true, message: 'Please input name!' },{min: 5, max: 50, message: '长度必须在 5 ～ 50', trigger: 'blur'}] }]"}]})],1),e("a-form-model-item",{attrs:{label:"描述",prop:"description"}},[e("a-input",{directives:[{name:"decorator",rawName:"v-decorator",value:["description"],expression:"['description']"}],attrs:{type:"textarea"}})],1),e("a-form-model-item",{attrs:{label:"标签",prop:"tags"}},[e("a-select",{key:t.componentKey,attrs:{"show-search":"",placeholder:"请选择标签",mode:"multiple"},on:{change:t.handleSelect},scopedSlots:t._u([{key:"dropdownRender",fn:function(a){return e("div",{},[e("v-nodes",{attrs:{vnodes:a}}),e("a-divider",{staticStyle:{margin:"4px 0"}}),e("div",{staticStyle:{padding:"4px 8px",cursor:"pointer"},on:{mousedown:t=>t.preventDefault(),click:t.addItemClick}},[e("a-icon",{attrs:{type:"plus"}}),t._v(" 添加新的标签 ")],1)],1)}}])},t._l(t.filteredSelectedOptions,(function(a,s){return e("a-select-option",{key:s,attrs:{value:a.id}},[e("div",{staticClass:"color-box",style:{"background-color":a.color}}),e("span",[t._v(t._s(a.name))])])})),1)],1),t._l(t.dynamicValidateForm.properties,(function(a,s){return e("a-form-model-item",t._b({key:a.key,attrs:{label:"参数"+t.incrementIndex(s),prop:"domains."+s+".value"}},"a-form-model-item",0===s?t.formItemLayout:{},!1),[e("a-input",{staticStyle:{width:"80%","margin-right":"8px"},attrs:{placeholder:"please input domain","label-col":{span:3},"wrapper-col":{span:20}},model:{value:a.value,callback:function(e){t.$set(a,"value",e)},expression:"domain.value"}}),t.dynamicValidateForm.properties.length>0?e("a-icon",{staticClass:"dynamic-delete-button",staticStyle:{display:"inline-grid"},attrs:{type:"minus-circle-o"},on:{click:function(e){return t.removeProperties(a)}}}):t._e()],1)})),e("a-form-model-item",t._b({},"a-form-model-item",t.formItemLayoutWithOutLabel,!1),[e("a-button",{staticStyle:{width:"100%"},attrs:{type:"dashed"},on:{click:t.addProperties}},[e("a-icon",{attrs:{type:"plus"}}),t._v(" 添加命令行参数 ")],1)],1)],2),e("div",{staticStyle:{margin:"auto",width:"93%"}},[e("a-upload-dragger",{attrs:{directory:!1,name:"file",multiple:!1,accept:".sh","before-upload":t.beforeUpload,"custom-request":t.uploadScriptFileAndPostScriptData,"file-list":t.fileList}},[e("p",{staticClass:"ant-upload-drag-icon"},[e("a-icon",{attrs:{type:"inbox"}})],1),e("p",{staticClass:"ant-upload-text"},[t._v(" 单击或拖动文件到此区域进行上传 ")]),e("p",{staticClass:"ant-upload-hint"},[t._v(" 只接受一个脚本文件 ")])])],1)],2),e("a-modal",{ref:"tagForm",attrs:{title:"添加一个新的标签","ok-text":"确认","cancel-text":"取消",action:"",maskClosable:!1,afterClose:t.initTagFormData},on:{ok:t.addTag},model:{value:t.childrenModelVisible,callback:function(e){t.childrenModelVisible=e},expression:"childrenModelVisible"}},[e("a-form-model",{ref:"ruleForm",attrs:{model:t.newTag,rules:t.childrenModelRules,"label-col":{span:3},"wrapper-col":{span:20}}},[e("a-form-model-item",{ref:"name",attrs:{label:"内容",prop:"name"}},[e("a-input",{model:{value:t.newTag.name,callback:function(e){t.$set(t.newTag,"name",e)},expression:"newTag.name"}})],1),e("a-form-model-item",{attrs:{label:"颜色",prop:"color"}},[e("a-input",{attrs:{type:"color"},model:{value:t.newTag.color,callback:function(e){t.$set(t.newTag,"color",e)},expression:"newTag.color"}})],1)],1)],1)],1)}),n=[],o={name:"AddScript",components:{VNodes:{functional:!0,render:(t,e)=>e.props.vnodes}},props:{openFlag:{type:Boolean,required:!0},filterOption:{type:Array,required:!0}},data(){return{loading:!1,visible:!1,childrenModelVisible:!1,childrenModelRules:{name:[{required:!0,message:"请输入标签内容",trigger:"blur"},{min:1,max:20,message:"长度必须在 1 ～ 20",trigger:"blur"}]},formLayout:"horizontal",form:this.$form.createForm(this,{name:"coordinated"}),dynamicValidateForm:{properties:[]},formItemLayout:{labelCol:{xs:{span:3},sm:{span:3}},wrapperCol:{xs:{span:20},sm:{span:20}}},formItemLayoutWithOutLabel:{wrapperCol:{xs:{span:22,offset:1},sm:{span:22,offset:1}}},tags:[],selectedItems:[],newTag:{name:"",color:"#61afd1"},fileList:[],componentKey:0}},watch:{openFlag:{handler:function(t,e){this.visible=t},deep:!0},filterOption:{handler:function(t,e){this.tags=t},deep:!0}},computed:{filteredSelectedOptions(){return this.tags.filter((t=>!this.selectedItems.includes(t.id)))}},mounted(){this.visible=this.openFlag,this.tags=this.filterOption},methods:{handleSelect(t){this.selectedItems=t},addItemClick(){this.childrenModelVisible=!0},addTag(){this.$refs.ruleForm.validate((t=>{if(!t)return!1;var e={data:this.newTag};this.$request.postTags(e).then((t=>{200===t.status?(this.$message.success("添加标签成功"),this.tags.push(t.data.data[0])):this.$message.error("添加标签失败")})),this.initTagFormData(),this.childrenModelVisible=!1}))},initTagFormData(){this.$refs.ruleForm&&this.$refs.ruleForm.resetFields()},removeProperties(t){let e=this.dynamicValidateForm.properties.indexOf(t);-1!==e&&this.dynamicValidateForm.properties.splice(e,1)},addProperties(){this.dynamicValidateForm.properties.push({value:"",key:Date.now()})},handleOk(t){this.loading=!0,t.preventDefault(),this.form.validateFields((async(t,e)=>{if(t)this.loading=!1;else{if(0===this.fileList.length)return this.$message.error("还没有上传脚本文件"),void(this.loading=!1);this.uploadScriptFileAndPostScriptData(e)}}))},uploadScriptFileAndPostScriptData(t){if(0===this.fileList.length)this.$message.warning("请上传文件");else{const e=this.fileList[0];let a=e.name.toLowerCase().substr(e.name.lastIndexOf("."));if(".sh"!==a)return this.$message.warning("请上传sh文件"),!1;this.fileList=[e];const s=new FormData;this.fileList.forEach((t=>{s.append("file",t)})),this.$request.uploadFile(s,"script").then((e=>{if(200===e.status){var a=e.data.data.path,s=[];this.dynamicValidateForm.properties.forEach((t=>{s.push(t.value)}));const r=this.selectedItems.map((t=>({id:t})));var i={data:{name:t.name,description:t.description,tags:r,path:a,properties:s}};this.$request.postScriptData(i).then((t=>{200===t.status?(this.$message.success("添加脚本成功"),this.$emit("submit",t.data.data),setTimeout((()=>{this.initFormData(),this.$emit("updateModelStatus",!1),this.loading=!1}),100)):(this.loading=!1,this.$message.error("添加脚本失败"))}))}else this.loading=!1,this.$message.error("上传脚本失败")}))}},beforeUpload(t){return new Promise(((e,a)=>{let s=t.name.toLowerCase().substr(t.name.lastIndexOf("."));return".sh"!==s?(this.$message.warning("请上传sh文件"),a(!1)):(this.fileList=[t],!1)}))},handleCancel(t){this.initTagFormData(),this.initFormData(),this.$emit("updateModelStatus",!1)},afterClose(t){this.initTagFormData(),this.initFormData(),this.$emit("updateModelStatus",!1)},incrementIndex(t){return parseInt(t)+1},initFormData(){this.form.resetFields(),this.tags=[],this.fileList=[],this.selectedItems=[],this.dynamicValidateForm={properties:[]},this.componentKey+=1}}},l=o,d=a(1001),c=(0,d.Z)(l,r,n,!1,null,"40e478b0",null),p=c.exports,h=a(35823),m=a.n(h),u=function(){var t=this,e=t._self._c;return e("div",[e("a-modal",{ref:"tagForm",attrs:{title:"添加一个新的标签","ok-text":"确认","cancel-text":"取消",action:"",maskClosable:!1,afterClose:t.handleCancel},on:{ok:t.addTag},model:{value:t.visible,callback:function(e){t.visible=e},expression:"visible"}},[e("a-form-model",{ref:"ruleForms",attrs:{model:t.newTag,rules:t.childrenModelRules,"label-col":{span:3},"wrapper-col":{span:20}}},[e("a-form-model-item",{ref:"name",attrs:{label:"内容",prop:"name"}},[e("a-input",{model:{value:t.newTag.name,callback:function(e){t.$set(t.newTag,"name",e)},expression:"newTag.name"}})],1),e("a-form-model-item",{attrs:{label:"颜色",prop:"color"}},[e("a-input",{attrs:{type:"color"},model:{value:t.newTag.color,callback:function(e){t.$set(t.newTag,"color",e)},expression:"newTag.color"}})],1)],1)],1)],1)},g=[],f={name:"AddTag",props:{openFlag:{type:Boolean,required:!0}},data(){return{loading:!1,visible:!1,childrenModelRules:{name:[{required:!0,message:"请输入标签内容",trigger:"blur"},{min:1,max:20,message:"长度必须在 1 ～ 20",trigger:"blur"}]},newTag:{name:"",color:"#61afd1"}}},watch:{openFlag:{handler:function(t,e){this.visible=t},deep:!0}},mounted(){this.visible=this.openFlag},methods:{addTag(){this.$refs.ruleForms.validate((t=>{if(!t)return!1;var e={data:this.newTag};this.$request.postTags(e).then((t=>{200===t.status?(this.$message.success("添加标签成功"),this.$emit("updateModelStatus",!1),this.$emit("submit",t.data.data)):this.$message.error("添加标签失败")})),this.initTagFormData()}))},initTagFormData(){this.$refs.ruleForms.resetFields()},handleCancel(t){this.$emit("updateModelStatus",!1),this.initTagFormData()},afterClose(t){this.$emit("updateModelStatus",!1),this.initTagFormData()}}},y=f,b=(0,d.Z)(y,u,g,!1,null,"72561f07",null),v=b.exports,w={name:"Script",components:{AddTag:v,AddScript:p,VNodes:{functional:!0,render:(t,e)=>e.props.vnodes}},data(){return{columns:[{title:"名称",width:200,dataIndex:"name",key:"name",scopedSlots:{customRender:"name"}},{title:"描述",width:350,dataIndex:"description",scopedSlots:{customRender:"description"}},{title:"标签",key:"tags",width:250,dataIndex:"tags",filters:[],scopedSlots:{customRender:"tags"}},{title:"创建时间",width:150,dataIndex:"created_at",key:"created_at",sorter:(t,e)=>new Date(t.created_at)-new Date(e.created_at)},{title:"Action",dataIndex:"",key:"x",scopedSlots:{customRender:"action"}}],paginationConfig:{defaultCurrent:1,defaultPageSize:5},allTags:[],tmpTags:[],scriptData:[],tableData:[],cacheData:[],editingKey:"",expandedRowKeys:[],filteredTags:[],tagsFilterDropdownVisible:!1,openAddScriptModel:!1,openAddTagModel:!1,fileList:[],componentKey:0,selectedTagsItems:""}},created(){this.getScriptData(),this.getAllTag()},methods:{getAllTag(){this.$request.getTags().then((t=>{200===t.status?this.allTags=t.data.data:this.$message.error("获取tags列表错误")}))},getScriptData(t){null==t&&(t=""),this.$request.getScriptData(t).then((t=>{if(200===t.status){this.scriptData=t.data.data;const e=[];this.scriptData.forEach((t=>{t.tags.forEach((t=>{const a=e.find((e=>e.text===t.name));a||e.push({text:t.name,value:t.name})})),t.key=t.id})),this.columns.forEach((t=>{"tags"===t.key&&(t.filters=e)})),this.tableData=[...this.scriptData],this.cacheData=this.tableData.map((t=>JSON.parse(JSON.stringify(t))))}}))},handleExpandedRowsChange(t){this.expandedRowKeys=t},handleChangeEdit(t,e,a,s){const i=[...this.tableData],r=i.find((t=>e===t.key));r&&("properties"===a?r[a][s]=t:r[a]=t,this.tableData=i)},edit(t){const e=[...this.tableData],a=e.find((e=>t===e.key));if(this.handleExpandedRowsChange([t]),this.editingKey=t,a){var s=[];this.allTags.forEach((t=>{a.tags.find((e=>t.id===e.id))||s.push(t)})),this.tmpTags=[...s],a.editable=!0,this.tableData=e}},save(t){const e=[...this.tableData],a=JSON.parse(JSON.stringify(this.cacheData)),s=e.find((e=>t===e.key)),i=a.find((e=>t===e.key));if(s&&i){let t=s.name.length;if(!(t>=5&&t<=50))return void this.$message.warning("名称长度应该在5～50之间");var r="";if(s.properties.forEach(((t,e)=>{if(""===t){parseInt(e);r="命令行参数存在空字段"}})),""!==r)return void this.$message.warning(r);var n={data:{id:s.id,name:s.name,description:s.description,properties:s.properties,path:s.path,tags:s.tags}};delete s.editable,this.tableData=e,Object.assign(i,s),this.cacheData=this.tableData.map((t=>JSON.parse(JSON.stringify(t)))),this.$request.putScriptData(n).then((t=>{200===t.status?this.$message.success("更新成功"):(this.$message.error("更新失败"),setTimeout((()=>{window.location.reload()}),3e3))}))}this.expandedRowKeys=[],this.editingKey=""},cancel(t){const e=[...this.tableData],a=e.find((e=>t===e.key));this.editingKey="",a&&(Object.assign(a,this.cacheData.find((e=>t===e.key))),delete a.editable,this.tableData=e,this.cacheData=this.tableData.map((t=>JSON.parse(JSON.stringify(t))))),this.expandedRowKeys=[]},confirmUpload(){this.$refs.uploadComponent.$el.querySelector("input[type=file]").click()},beforeUpload(t){let e=t.name.toLowerCase().substr(t.name.lastIndexOf("."));return".sh"!==e?(this.$message.warning("请上传sh文件"),!1):(this.fileList=[t],!0)},uploadUpdateScriptFileAndOther(t){if(0!==this.fileList.length){const e=this.fileList[0];let a=e.name.toLowerCase().substr(e.name.lastIndexOf("."));if(".sh"!==a)return this.$message.warning("请上传sh文件"),!1;this.fileList=[e];const s=new FormData;this.fileList.forEach((t=>{s.append("file",t)})),this.$request.uploadFile(s,"script").then((e=>{if(200===e.status){const a=e.data.data.path;this.handleChangeEdit(a,t,"path"),this.fileList=[],this.$message.success("更新脚本文件成功")}else this.$message.error("更新脚本文件失败")}))}},removePropertiesItems(t,e){const a=[...this.tableData],s=a.find((e=>t===e.key));s&&(s["properties"].splice(e,1),this.tableData=a)},addPropertiesItems(t){const e=[...this.tableData],a=e.find((e=>t===e.key));a&&(a["properties"].push(""),this.tableData=e)},removeTagItem(t,e,a){a.preventDefault();const s=[...this.tableData],i=s.find((t=>e===t.key));if(i){const e=[];i.tags.forEach((a=>{a.id!==t&&e.push(a)}));const a=this.allTags.find((e=>t===e.id));var r=[...this.tmpTags];r.push(a),this.tmpTags=[...r],i.tags=e,this.tableData=s}},addTags(){this.openAddTagModel=!0},filterOption(t,e){return e.componentOptions.children[1].text.toLowerCase().indexOf(t.toLowerCase())>=0},handleEditModeSelectTag(t,e){this.selectedTagsItems="";var a=this.editingKey;const s=this.tableData.slice(),i=s.find((t=>a===t.key));var r=this.tmpTags.find((e=>t===e.id));if(i&&r){i.tags.push(r),this.tableData=s,this.componentKey+=1;var n=[];this.allTags.forEach((t=>{i.tags.find((e=>t.id===e.id))||n.push(t)})),this.tmpTags=[...n]}},deleteScript(t){this.$confirm({title:"确认删除脚本吗?",content:"将会删除您的脚本文件",okText:"删除",okType:"danger",cancelText:"不了",onOk:()=>{var e=this.cacheData.find((e=>t===e.key)),a={data:{id:e.id}};this.$request.deleteScriptData(a).then((e=>{if(200===e.status){const e=[...this.scriptData];this.scriptData=e.filter((e=>e.key!==t)),this.tableData=[...this.scriptData],this.$message.success("删除成功");const s=[];this.scriptData.forEach((t=>{t.tags.forEach((t=>{const e=s.find((e=>e.text===t.name));e||s.push({text:t.name,value:t.name})})),t.key=t.id}));var a=this.columns;a.forEach((t=>{"tags"===t.key&&(t.filters=s)})),this.$set(this,"columns",[...a]),this.tableData=[...this.scriptData],this.cacheData=this.tableData.map((t=>JSON.parse(JSON.stringify(t))))}else this.$message.error("删除失败")}))},onCancel(){console.log("Cancel")}})},onSearch(t){var e="?key="+t;this.getScriptData(e)},handleChange(t,e){var a=e.tags;0!==e.tags.length?this.tableData=this.scriptData.filter((t=>t.tags.some((t=>a.includes(t.name))))):this.tableData=[...this.scriptData]},addScript(){var t=[...this.allTags];this.allTags=[],this.allTags=t,this.openAddScriptModel=!0},runScript(t){var e=this.$message,a=e.loading("正在执行脚本，您可以继续进行其他操作，但不要刷新页面",0),s=this.scriptData.find((e=>t===e.key)),i={data:{script_id:s.id}};this.$request.runScript(i).then((t=>{if(200===t.status){setTimeout(a,0),e.success("脚本执行成功",2.5);const i="run_"+s.name+"_"+this.getNow();m()(t.data,i)}else setTimeout(a,0),e.error("脚本执行失败",2.5)}))},updateTable(t){if(null!==t){var e=t[0];e.key=e.id,this.scriptData.unshift(e),this.$set(this,"scriptData",[...this.scriptData]);var a=this.columns;a.forEach((t=>{if("tags"===t.key){var a=[...t.filters];e.tags.forEach((t=>{const e=a.find((e=>e.text===t.name));e||a.push({text:t.name,value:t.name})})),t.filters=a}})),this.$set(this,"columns",[...a]),this.tableData=[...this.scriptData],this.cacheData=this.tableData.map((t=>JSON.parse(JSON.stringify(t))))}},updateTags(t){if(null!==t){const e=t[0];e.key=e.id,this.allTags.unshift(e),this.tmpTags.unshift(e),this.$set(this,"tags",[...this.allTags])}},getAddScriptModelStatus(t){this.openAddScriptModel=t},getAddTagModelStatus(t){this.openAddTagModel=t},downloadScriptFile(t){let e=t.substring(t.lastIndexOf("/")+1);this.$request.downloadFile(t).then((t=>{200===t.status&&m()(t.data,e)}))},getNow(){var t=new Date,e=t.getFullYear(),a=("0"+(t.getMonth()+1)).slice(-2),s=("0"+t.getDate()).slice(-2),i=("0"+t.getHours()).slice(-2),r=("0"+t.getMinutes()).slice(-2),n=("0"+t.getSeconds()).slice(-2);return e+a+s+i+r+n}}},S=w,k=(0,d.Z)(S,s,i,!1,null,"4888e2e1",null),D=k.exports},35823:function(t){t.exports=function(t,e,a,s){var i="undefined"!==typeof s?[s,t]:[t],r=new Blob(i,{type:a||"application/octet-stream"});if("undefined"!==typeof window.navigator.msSaveBlob)window.navigator.msSaveBlob(r,e);else{var n=window.URL&&window.URL.createObjectURL?window.URL.createObjectURL(r):window.webkitURL.createObjectURL(r),o=document.createElement("a");o.style.display="none",o.href=n,o.setAttribute("download",e),"undefined"===typeof o.download&&o.setAttribute("target","_blank"),document.body.appendChild(o),o.click(),setTimeout((function(){document.body.removeChild(o),window.URL.revokeObjectURL(n)}),200)}}}}]);