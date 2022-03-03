<template>
  <div class="park" v-if="park">
    <a href="/" @click.exact.prevent="$router.push('/')">Close</a>
    <h2>{{ park.Title }}</h2>
    <ul class="park__features">
      <li>{{ park.FeatureOnOffLeash }}</li>
    </ul>
    <p class="park__notes">{{ park.Notes }}</p>
    <p class="park__provider">Managed by <strong>{{ park.Provider }}</strong></p>
    <h3>Show us your doggo</h3>
    <span ref="parkPhoto"><span v-html="park.Photo"></span></span>
    <input type="file" accept="image/jpeg, image/png" @change="onFileChanged">
    <button ref="imageUpload" style="display: none;" @click="onUpload">Upload</button>
  </div>
</template>

<script>

import axios from "axios";

export default {
  computed: {
    park() {
      return this.$store.state.parks.find(park => park.ID === parseInt(this.$route.params.id, 10));
    },
    parks() {
      return this.$store.state.parks;
    }
  },
  data() {
    return {
      selectedFile: null
    }
  },
  methods: {
    onFileChanged (event) {
      this.selectedFile = event.target.files[0];
      if (this.selectedFile && (this.selectedFile.type == 'image/jpeg' || this.selectedFile.type == 'image/png')) {
        this.$refs["imageUpload"].setAttribute('style', 'display: inline');
      }
      else {
        this.$refs["imageUpload"].setAttribute('style', 'display: none');
      }
    },
    onUpload() {
        if (this.selectedFile.type == 'image/jpeg' || this.selectedFile.type == 'image/png') {
          const fd = new FormData();
          var parkPhoto = this.$refs["parkPhoto"];
          fd.append('image', this.selectedFile, this.selectedFile.name);
          axios.post('api/v1/parks/'+this.park.ID+'/upload', fd)
            .then(function (res) {
              if (res.status == 200) {
                console.log(parkPhoto);
                parkPhoto.firstChild.firstChild.setAttribute('src', res.data);
              }

            console.log(res);
          });
        }
        else {
          console.log('invalid file');
        }
    }
  }
};
</script>

<style>
  .park {
    padding: 20px;
  }
</style>
