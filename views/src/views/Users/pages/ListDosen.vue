<template>
  <v-container>
    <v-app>
      <v-snackbar bottom :timeout="timeout" color="success" v-model="snackbar">
        <v-icon left color="white">done</v-icon>Surat berhasil dikirim
        <v-btn text @click="snackbar = false">Close</v-btn>
      </v-snackbar>

      <v-card-title>
        <v-btn dark color="blue-grey darken-3" @click="dialogAdd = true">
          <v-icon left>add</v-icon>Tambah Dosen
        </v-btn>
        <v-spacer></v-spacer>
        <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
      </v-card-title>

      <v-data-table :headers="headers" :items="dosen" :search="search" class="elevation-1">
        <template v-slot:items="props">
          <td>{{ props.item.nama }}</td>
          <td class="text-xs-right">{{ props.item.nidn}}</td>
          <td class="text-xs-right">{{ props.item.email}}</td>
          <td class="text-xs-right">{{ props.item.kategori }}</td>
        </template>

        <template v-slot:item.action="{ item }">
          <v-btn class="ma-2" dark color="primary" @click="kirimConfirm(item.id, item.nama)">
            <v-icon left>mdi-mail</v-icon>Kirim surat
          </v-btn>
          <v-btn
            class="ma-2"
            outlined
            color="success"
            @click="updateConfirmation(item.id, item.nama, item.nidn, item.email, item.kategori), dialogUpdate= true"
          >
            <v-icon left>mdi-pencil</v-icon>Edit
          </v-btn>
          <v-btn class="ma-2" dark color="error" @click="deleteConfirm(item.id, item.nama)">
            <v-icon left>mdi-delete</v-icon>Hapus
          </v-btn>
        </template>
      </v-data-table>

      <v-dialog v-model="dialogAdd" persistent max-width="600px">
        <v-card class="pa-5">
          <h2 class="mb-5">Tambah user</h2>
          <v-divider class="mb-5"></v-divider>
          <v-card-text>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-text-field
                outlined
                v-model="nama"
                :rules="[v => !!v || 'Name is required']"
                label="Nama"
              ></v-text-field>
              <v-text-field outlined v-model="email" :rules="emailRules" label="Email"></v-text-field>
              <v-text-field
                outlined
                v-model="nidn"
                :rules="[v => !!v || 'NIDN is required']"
                label="NIDN"
              ></v-text-field>
              <v-text-field
                outlined
                v-model="password"
                :append-icon="show ? 'visibility' : 'visibility_off'"
                :rules="passwordRules"
                :type="show ? 'text' : 'password'"
                @click:append="show = !show"
                label="Password"
              ></v-text-field>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn outlined @click="reset">Batal</v-btn>
            <v-btn color="primary" :disabled="!valid" @click="saveData">Simpan</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogUpdate" persistent max-width="600px">
        <v-card class="pa-5">
          <h2 class="mb-5">Sunting user</h2>
          <v-divider class="mb-5"></v-divider>
          <v-card-text>
            <v-form ref="form">
              <v-text-field outlined v-model="nama" label="Nama"></v-text-field>
              <v-text-field outlined v-model="nidn" label="NIDN"></v-text-field>
              <v-text-field outlined v-model="email" label="Email"></v-text-field>
              <!-- <v-text-field outlined v-model="kategori" label="Kategori"></v-text-field> -->
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn outlined @click="reset">Batal</v-btn>

            <v-btn color="primary" @click="updateData">Simpan</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogConfirm" persistent max-width="500px">
        <v-divider></v-divider>
        <v-card class="pa-5">
          <v-card-text>
            <h1 style="line-height:1.5">Apakah kamu yakin ingin menghapus artikel ini?</h1>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn outlined @click="dialogConfirm = false">Batal</v-btn>

            <v-btn color="error" @click="deleteData(id), dialogConfirm= false">Oke</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- kirim surat dialog -->
      <v-dialog v-model="dialogSurat" persistent max-width="600px">
        <v-divider></v-divider>
        <v-card class="pa-5">
          <v-card-text>
            <h2 class="mb-5">
              Mengirim surat ke
              <span class="purple--text font-weight-bold">{{ nama }}?</span>
            </h2>
            <v-divider class="mb-5"></v-divider>
            <v-form ref="form">
              <v-text-field outlined v-model="judul_surat" label="Judul surat"></v-text-field>
              <v-select
                outlined
                v-model="jenis_surat"
                :rules="[v => !!v || 'This is required']"
                :items="tipe_items"
                label="Tipe surat..."
                item-text="name"
                item-value="value"
                required
              ></v-select>
              <v-file-input
                v-model="file_surat"
                outlined
                @change="onFilePicked"
                ref="files"
                type="file"
                placeholder="Unggah dokumen disini..."
                prepend-icon="mdi-paperclip"
              >
                <!-- <template v-slot:selection="{ text }">
                  <v-chip small label color="primary">{{ text }}</v-chip>
                </template>-->
              </v-file-input>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn outlined @click="reset">Batal</v-btn>
            <v-btn color="primary" @click="sendData">Kirim surat</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-app>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    headers: [
      {
        text: "Nama",
        align: "left",
        sortable: true,
        value: "nama"
      },
      { text: "Nidn", value: "nidn" },
      { text: "Email", value: "email" },
      {
        text: "Kategori",
        value: "kategori",
        filter: value => {
          return value === "Dosen";
        }
      },
      { text: "Action", value: "action" }
    ],
    tipe_items: [
      { name: "SK Penguji", value: "SK Penguji" },
      { name: "SK Mengajar", value: "SK Mengajar" },
      { name: "SK Dosen Pembimbing", value: "SK Dosen Pembimbing" }
    ],
    dosen: [],
    show: false,
    valid: true,
    lazy: false,
    passwordRules: [
      v => !!v || "Password is required",
      v => v.length >= 6 || "Password must be greater than 6 characters"
    ],
    emailRules: [
      v => !!v || "E-mail is required",
      v => /.+@.+/.test(v) || "E-mail must be valid"
    ],
    search: "",
    tipe_surat: null,
    dialogAdd: false,
    dialogConfirm: false,
    dialogUpdate: false,
    dialogSurat: false,
    snackbar: false,
    timeout: 2000,
    nama: "",
    nidn: "",
    email: "",
    password: "",
    kategori: "Dosen",
    judul_surat: "",
    jenis_surat: "",
    file_surat: null,
    suratURL: "",
    suratFile: "",
    suratType: "",
    id_user: "",
    filter: "Dosen"
  }),
  created() {
    this.getData();
    this.getCurrentUser();
  },
  methods: {
    getData: function() {
      this.axios
        .get("/user")
        .then(res => {
          this.dosen = res.data.data;
          console.log(this.dosen);
        })
        .catch(err => {
          console.log(err);
        });
    },
    getCurrentUser: function() {
      let userData = JSON.parse(localStorage.getItem("user"));
      this.currentUser = userData[0];
    },
    reset() {
      this.$refs.form.reset();
      this.dialogAdd = false;
      this.dialogUpdate = false;
      this.dialogSurat = false;
      this.dialogConfirm = false;
    },
    saveData() {
      let data = {
        nama: this.nama,
        nidn: this.nidn,
        email: this.email,
        password: this.password,
        kategori: this.kategori
      };
      this.axios
        .post("/user", data)
        .then(res => {
          this.reset();
          this.getData();
        })
        .catch(err => {
          console.log(err);
        });
    },
    updateData() {
      let data = {
        id: this.id,
        nama: this.nama,
        nidn: this.nidn,
        email: this.email,
        kategori: this.kategori
      };
      this.axios
        .put("/user", data)
        .then(res => {
          this.dialogUpdate = false;
          this.getData();
          this.$refs.form.reset();
        })
        .catch(err => {
          console.log(err);
        });
    },
    deleteData(ida) {
      let data = {
        id: ida
      };
      this.axios
        .post("/hapususer", data)
        .then(res => {
          this.dialogConfirm = false;
          this.getData();
          this.$refs.form.reset();
        })
        .catch(err => {
          console.log(err);
        });
    },
    checkTipe(value) {
      this.jenis_surat = value;
    },
    sendData() {
      let data_surat = {
        judul_surat: this.judul_surat,
        jenis_surat: this.jenis_surat,
        id_user: this.id,
        file_surat: this.suratFile,
        ext: this.suratType
      };

      this.axios
        .post("/surat", data_surat)
        .then(res => {
          this.dialogSurat = false;
          this.snackbar = true;
          console.log(res);
        })
        .catch(err => {
          console.log(err);
        });
    },
    deleteConfirm(id) {
      this.id = id;
      this.dialogConfirm = true;
    },
    kirimConfirm(id, nama) {
      this.id = id;
      this.nama = nama;
      this.dialogSurat = true;
    },
    updateConfirmation(id, nama, nidn, email, kategori) {
      this.id = id;
      this.nama = nama;
      this.nidn = nidn;
      this.email = email;
      this.kategori = kategori;
    },
    onFilePicked(e) {
      const fr = new FileReader();
      fr.readAsDataURL(e);
      fr.addEventListener("load", () => {
        this.suratURL = e;
        this.suratFile = fr.result; // this is an image file that can be sent to server...
        if (
          e.type ===
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        ) {
          this.suratType = "application/docx";
        } else if (e.type === "application/msword") {
          this.suratType = "application/doc";
        } else {
          this.suratType = e.type;
        }
        console.log(fr.result);
        console.log(e.type);
      });
    }
  }
};
</script>

<style scoped>
.v-btn {
  margin-left: 0px;
  margin-right: 0px;
}

.v-card__title {
  padding: 18px 0px;
}
</style>