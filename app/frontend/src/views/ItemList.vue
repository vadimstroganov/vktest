<template>
  <div>
    <h1 class="ui header center aligned">Список товаров</h1>

    <div class="ui text container">
      <div class="ui four item menu">
        <a @click="setSort('id', 'asc')" class="item">ID <i class="sort amount up icon"></i></a>
        <a @click="setSort('id', 'desc')" class="item">ID <i class="sort amount down icon"></i></a>
        <a @click="setSort('cost', 'asc')" class="item">Цена <i class="sort amount up icon"></i></a>
        <a @click="setSort('cost', 'desc')" class="item">Цена <i class="sort amount down icon"></i></a>
      </div>
    </div>

    <div class="ui grid cards center aligned">
      <div v-for="item in items" class="ui card" :key="item.id">
        <div class="image">
          <img v-if="item.image" :src="item.image">
          <img v-else src="../assets/placehold.png">
        </div>
        <div class="content">
          <a class="header">{{ item.name }}</a>
          <div class="description">{{ item.description }}</div>
          <div class="description">Цена - {{ item.cost / 100 }} ₽</div>
        </div>
        <div class="extra content">
          <router-link :to="{ name: 'editItem', params: { id: item.id } }">
            <i class="pencil icon"></i> Редактировать
          </router-link>
        </div>
      </div>

      <infinite-loading @infinite="infiniteHandler" ref="infiniteLoading"></infinite-loading>
    </div>
  </div>
</template>

<script>
import InfiniteLoading from 'vue-infinite-loading'

export default {
  data: () => ({
    items: [],
    page: 1,
    sortColumn: 'id',
    sortDirection: 'asc',
    end: false
  }),

  components: {
    InfiniteLoading
  },

  methods: {
    infiniteHandler($state) {
      this.fetchItems().then(items => {
        if (items.length > 0) {
          this.page += 1
          this.items = this.items.concat(items)
          $state.loaded()
        } else {
          $state.complete()
        }
      })
    },

    async fetchItems () {
      let params = {
        sort_column: this.sortColumn,
        sort_type: this.sortDirection,
        page: this.page
      }

      let response = await this.axios.get('items', { params: params })
      return response.data.items
    },

    setSort (column, direction) {
      this.page = 1
      this.sortColumn = column
      this.sortDirection = direction

      this.items = []
      this.fetchItems().then(items => {
        this.page += 1
        this.items = this.items.concat(items)
      })
    }
  }
}
</script>

<style scoped>
  .infinite-loading-container {
    display: block;
    width: 100%;
  }
</style>
