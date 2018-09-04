<template>
  <div>
    <h1 class="ui header center aligned">Список товаров</h1>

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
    </div>
  </div>
</template>

<script>
  export default {
    data: () => ({
      items: []
    }),

    created() {
      this.axios.get('items').then((response) => {
        this.items = response.data.items
      })
    }
  }
</script>
