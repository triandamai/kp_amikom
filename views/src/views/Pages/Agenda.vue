<template>
  <div>
    <NavBar></NavBar>
    <div class="container__p">
      <div class="container__p--inner">
        <div class="blog-wrapper">
          <!-- <div class="blog-wrapper__heading">
            <h1>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="36"
                height="36"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"
                />
              </svg> Berita terkini
            </h1>
          </div> -->
          <div class="blog-wrapper__grid" v-for="(item, index) in items" :key="item.id + '_' + index">
            <div class="blog-wrapper__grid--date">
              <div class="tgl_p">
                {{ item.date.slice(-2) }}
              </div>
              <div class="thn_p">
                {{ item.date.substring(0, 4) }}
              </div>
            </div>
            <div class="blog-wrapper__grid--body">
              <h2>{{ item.title_post }}</h2>
              <p v-html="$options.filters.truncate(item.body_post, 250)"></p>
             <router-link :to="'/agenda/' + item.id" class="btn-go">Baca selengkapnya</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer></Footer>
  </div>
</template>

<script>
// import Header from "../../components/Header";
import NavBar from "../../components/NavBar";
import Footer from "../../components/Footer";

export default {
  name: "Blog",
  components: {
    // Header,
    NavBar,
    Footer
  },
  data: () => ({
    items: []
  }),
  mounted() {
    this.getArticle();
  },
  filters: {
    truncate: function(value, limit) {
      if (value.length > limit) {
        value = value.substring(0, limit - 3) + "...";
      }
      return value;
    }
  },
  methods: {
    getArticle: function() {
      this.axios
        .get("/artikel?kategori=Event")
        .then(response => {
          this.items = response.data.data;
        })
        .catch(err => {
          // console.log(err);
        });
    }
  },
  metaInfo: {
    title:
      "Berita Terkini | Prodi Teknik Informatika - Universitas Amikom Purwokerto",
    // override the parent template and just use the above title only
    titleTemplate: null
  }
};
</script>

<style lang="scss" scoped>
@import "../../assets/css/variables";
@import "../../assets/css/mixins";

.blog-wrapper {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-gap: 3.5em;
  margin-bottom: 2.5em;

  @include bp-medium() {
    grid-template-columns: 1fr;

    grid-gap: 1.5em;
  }

  &__heading {
    grid-column: 1/-1;

    h1 {
      svg {
        vertical-align: text-top;
        margin-right: 0.5em;
        @include bp-medium() {
          vertical-align: -10px;
          width: 25px;
        }
      }

      @include bp-medium() {
        font-size: 1.4em;
      }
    }
  }

  &__grid {
    display: grid;
    grid-template-columns: 100px 1fr;

    img {
      @include box-shadow(4px, 8px, 15px, 0, lighten(#000, 90));
      -webkit-border-radius: 8px;
      -moz-border-radius: 8px;
      border-radius: 8px;
      margin-bottom: 1.5em;
    }

    @include bp-medium() {
      margin:0.85em 0;
    }

    &--date {
      margin-bottom: 1em;
      font-family: $heading-font;

      .tgl_p {
        font-weight: bold;
        font-size: 2em;
      }

      .thn_p {
        color:$primary-color;
        font-weight: bold;
      }
    }

    .btn-go {
      display: block;
      margin: 2em 0 0 0;
      max-width: 12rem;
      text-decoration: none;
      font-family: $heading-font;
      font-size: 0.85em;
      font-weight: bold;
      text-align: center;
      padding: 0.65em 0.45em;
      background-color: $primary-color;
      border: 3px solid $primary-color;
      color: $white;
      -webkit-border-radius: 30px;
      -moz-border-radius: 30px;
      border-radius: 30px;
      transition: 0.5s;
      -moz-transition: 0.5s;

      &:hover {
        background-color: $black;
      }
    }
  }
}
</style>


