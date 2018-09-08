<template>
  <div>
    <h1 class="ui header center aligned">{{ action === "update" ? 'Редактирование' : 'Создание' }} товара</h1>

    <div class="ui container">
      <form class="ui form">
        <div class="field">
          <div class="ui grid">
            <div class="thirteen wide field">
              <label>Название</label>
              <input type="text" name="first-name" v-model="form.name">
            </div>

            <div class="three wide field">
              <label>Цена</label>
              <input type="number" name="first-name" v-model="form.cost">
            </div>
          </div>
        </div>

        <div class="field">
          <div class="ui grid">
            <div class="three wide field">
              <img v-if="form.imageUrl" :src="form.imageUrl" class="image">
              <img v-else src="../assets/placehold.png" class="image">
            </div>

            <div class="thirteen wide field">
              <label>Описание</label>
              <textarea v-model="form.description"></textarea>
            </div>
          </div>
        </div>

        <div class="field">
          <label>Изображение</label>
          <input type="file" @change="onFileSelected">
        </div>

        <button class="ui primary button" v-on:click="formHandler" v-bind:class="loading ? 'loading' : 'not-loading'">Сохранить</button>
        <button class="ui red button" v-on:click="deleteItemHandler">Удалить</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    form: {
      name: null,
      description: null,
      cost: null,
      imageUrl: null,
      selectedFile: null
    },
    loading: false
  }),

  props: ['action'],

  mounted () {
    if (this.$route.params.id) {
     this.fetchItem(this.$route.params.id).then(item => {
        this.form.name = item.name
        this.form.description = item.description,
        this.form.cost = item.cost
        this.form.imageUrl = item.image
      })
    }
  },

  methods: {
    formHandler (e) {
      e.preventDefault()
      this.loading = true

      let self = this
      let params = {...this.form, ...{ id: this.$route.params.id }}

      if (self.action === 'update') {
        this.updateItem(params).then(item => {
          this.form.imageUrl = item.image
          this.loading = false
          this.$swal(`Товар - ${item.name}, обновлен`, { icon: 'success' })
        }) 
      } else {
        this.createItem(params).then(item => {
          this.loading = false

          this.$swal(`Товар - ${item.name}, создан`, {icon: 'success'}).then(() => {
            location.href = `/items/${item.id}/edit`
          })
        })
      }
    },

    deleteItemHandler (e) {
      e.preventDefault()

      this.$swal({
        title: 'Подтвердите действие',
        text: `Вы хотите удалить товар - ${this.form.name}`,
        icon: 'warning',
        buttons: ['Отмена', 'Удалить'],
        dangerMode: true
      }).then((willDelete) => {
        if (willDelete) {
          this.deleteItem(this.$route.params.id).then(() => {
            location.href = `/`
          })
        }
      })
    },

    onFileSelected (e) {
      this.form.selectedFile = e.target.files[0]
    },

    async fetchItem (id) {
      let response = await this.axios.get('items/show', { params: { id: id } })
      return response.data.item
    },

    async createItem(params) {
      const headers = {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }

      let fd = new FormData
      fd.append('name', params['name'])
      fd.append('description', params['description'])
      fd.append('cost', params['cost'])
      fd.append('image', params['selectedFile'])

      let response = await this.axios.post('items/create', fd, headers)
      return response.data.item
    },

    async updateItem(params) {
      const headers = {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }

      let fd = new FormData
      fd.append('id', params['id'])
      fd.append('name', params['name'])
      fd.append('description', params['description'])
      fd.append('cost', params['cost'])
      fd.append('image', params['selectedFile'])

      let response = await this.axios.post('items/update', fd, headers)
      return response.data.item
    },

    async deleteItem(id) {
      let fd = new FormData
      fd.append('id', id)

      let response = await this.axios.post('items/delete', fd)
      return response.data
    }
  }
}
</script>

<style scoped>
.image {
  width: 100%;
}
</style>
