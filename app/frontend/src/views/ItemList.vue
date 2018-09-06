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
      <div v-for="item in items" class="ui card">
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
          <a>
            <i class="pencil icon"></i> Edit
          </a>
          <a>
            <i class="trash icon"></i> Destroy
          </a>
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
      offset: 0,
      itemsPerPage: 50,
      end: false,
      sortColumn: 'id',
      sortDirection: 'asc'
    }),

    components: {
      InfiniteLoading
    },

    methods: {
      infiniteHandler($state) {
        this.fetchItems().then(items => {
          if (items.length > 0) {
            this.offset += this.itemsPerPage
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
          offset: this.offset
        }

        let response = await this.axios.get('items', { params: params })
        return response.data.items
      },

      setSort (column, direction) {
        this.offset = 0
        this.sortColumn = column
        this.sortDirection = direction

        this.items = []
        this.fetchItems().then(items => {
          this.offset += this.itemsPerPage
          this.items = this.items.concat(items)
        })
      }
    }
  }
</script>
