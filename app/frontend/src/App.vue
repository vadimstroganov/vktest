<template>
  <div>
    <div class="ui text container">
      <div class="ui two item menu">
        <router-link class="item" to="/">Все товары</router-link>
        <router-link class="item" to="/items/new">Добавить товар</router-link>
      </div>
    </div>

    <router-view/>
  </div>
</template>

<style lang="scss">
  @import './assets/style.scss';
</style>
