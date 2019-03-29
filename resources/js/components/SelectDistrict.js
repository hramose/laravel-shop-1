// 從安裝的套件中加載數據
const addressData = require('china-area-data/v3/data');
// 引入 lodash, lodash 是一個實用的工具套件，提供很多常用的方法
import _ from 'lodash';

// 註冊一個名為 select-district 的 Vue 組件
Vue.component('select-district', {
  // 定義組件的屬性
  props: {
    // 用來初始化省市區的值，在編輯時會用到
    initValue: {
      type: Array, // 格式是陣列
      default: () => ([]), // 默認是空陣列
    }
  },
  // 定義了這個組件內的數據
  data() {
    return {
      provinces: addressData['86'], // 省列表
      cities: {}, // 城市列表
      districts: {}, // 地區列表
      provinceId: '', // 目前選中的省
      cityId: '', // 目前選中的市
      districtId: '', // 目前選中的區
    };
  },

  // 定義觀察器，對應屬性變更時會觸發對應的觀察器函數
  watch: {
    // 當選擇的省發生改變時觸發
    provinceId(newVal) {
      if (!newVal) {
        this.cities = {};
        this.cityId = '';
        return;
      }
      // 將城市列表設為當前省下的城市
      this.cities = addressData[newVal];
      // 如果當前選中的城市不在當前省下，則將選中城市清空
      if (!this.cities[this.cityId]) {
        this.cityId = '';
      }
    },
    // 當選擇的市發生改變時觸發
    cityId(newVal) {
      if (!newVal) {
        this.districts = {};
        this.districtId = '';
        return;
      }
      // 將地區列表設為當前城市下的地區
      this.districts = addressData[newVal];
      // 如果當前選中的地區不在當前城市下，則將選中地區清空
      if (!this.districts[this.districtId]) {
        this.districtId = '';
      }
    },
    // 當選擇的區發生改變時觸發
    districtId() {
      // 觸發一個名為 change 的 Vue 事件，事件的值就是當前選中的省市區名稱，格式為陣列
      this.$emit('change', [this.provinces[this.provinceId], this.cities[this.cityId], this.districts[this.districtId]]);
    },
  },

  // 組建初始化時會使用這個方法
  created() {
    this.setFromValue(this.initValue);
  },
  methods: {
    //
    setFromValue(value) {
      // 過濾掉空值
      value = _.filter(value);
      // 如果陣列長度為0，則將省清空（由於我們定義了觀察器，會聯動觸發將城市和地區清空）
      if (value.length === 0) {
        this.provinceId = '';
        return;
      }
      // 從當前省列表中找到與陣列第一個元素同名的項目的索引
      const provinceId = _.findKey(this.provinces, o => o === value[0]);
      // 沒找到，清空省的值
      if (!provinceId) {
        this.provinceId = '';
        return;
      }
      // 找到了，將當前省設置成對應的ID
      this.provinceId = provinceId;
      // 由於觀察器的作用，這個時候程式列表已經變成了對應省的城市列表
      // 從當前程式列表找到與陣列第二個元素同名的項目的索引
      const cityId = _.findKey(addressData[provinceId], o => o === value[1]);
      // 沒找到，清空城市的值
      if (!cityId) {
        this.cityId = '';
        return;
      }
      // 找到了，將當前城市設置成對應的ID
      this.cityId = cityId;
      // 由於觀察器的作用，這個時候地區列表已經變成了對應城市的地區列表
      // 從當前地區列表找到與陣列第三個元素同名的項目的索引
      const districtId = _.findKey(addressData[cityId], o => o === value[2]);
      // 沒找到，清空地區的值
      if (!districtId) {
        this.districtId = '';
        return;
      }
      // 找到了，將當前地區設置成對應的ID
      this.districtId = districtId;
    }
  }
});
