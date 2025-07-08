<x-app-layout>
    <script src="https://unpkg.com/alpinejs" defer></script>
    {{-- Alpine.js logic --}}
    <script>
        window.productTable = function () {
            return {
                products: [],
                existingProducts: [],
                lastIndex: 0,
                $refsMap: {},
                init() {
                const data = @json($produkList);
                this.products = data.map(item => ({
                    name: item.category,
                    categories: item.products.map(p => ({
                    name: p.name,
                    image: null,
                    imageUrl: p.image ? `{{ asset('storage') }}/${p.image}` : null
                    }))
                }));

                // if (this.products.length > 0) {
                //     document.getElementById('batalkan_btn').style.display = 'inline-block';
                //     document.getElementById('btn_save').style.display = 'inline-block';
                // }
                },
                // Tambah produk baru ke atas
                addProduct() {
                    if (this.products.length < 5) {
                        this.products.push({
                            name: '',
                            desc: '',
                            categories: [{ name: '', image: null, imageUrl: null }]
                        });

                        $('#batalkan_btn').show('slow');
                        $('#btn_save').show('slow');
                    }
                },
                addOldCategory(index) {
                    // produk lama dari backend, langsung tambahin dari produkList
                    if (!this.products[index]) {
                        this.products[index] = {
                            name: @json($produkList)[index].category,
                            desc: '',
                            categories: []
                        };
                    }

                    if (this.products[index].categories.length < 3) {
                        this.products[index].categories.push({ name: '', image: null, imageUrl: null });
                    }
                },

                getRowNumber(index, cIndex) {
                    let offset = {{ count($produkList) }};
                    return offset + index + 1;
                },

                // Hapus seluruh produk
                removeProduct(index) {
                    this.products.splice(index, 1);
                },

                // Tambah kategori dalam produk
                addCategory(pIndex) {
                    if (this.products[pIndex].categories.length < 3) {
                        this.products[pIndex].categories.push({ name: '', image: null, imageUrl: null });
                    }
                },

                // Hapus kategori dari produk
                removeCategory(pIndex, cIndex) {
                    this.products[pIndex].categories.splice(cIndex, 1);
                    if (this.products[pIndex].categories.length === 0) {
                        this.products[pIndex].categories.push({ name: '', image: null, imageUrl: null });
                    }
                },

                // Upload gambar → preview via FileReader
                uploadImage(event, pIndex, cIndex) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (!['image/jpeg', 'image/png'].includes(file.type)) {
                        alert('Format file tidak valid (hanya JPG/PNG)');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = () => {
                        this.products[pIndex].categories[cIndex].image = file;
                        this.products[pIndex].categories[cIndex].imageUrl = reader.result;
                    };
                    reader.readAsDataURL(file);
                },

                // Konfirmasi hapus gambar pakai SweetAlert
                confirmDeleteImage(pIndex, cIndex) {
                    const self = this;
                    Swal.fire({
                        title: 'Apakah Anda Yakin untuk Menghapus Gambar?',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batalkan',
                        confirmButtonColor: '#D22B2B',
                        cancelButtonColor: '#808080'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            self.products[pIndex].categories[cIndex].image = null;
                            self.products[pIndex].categories[cIndex].imageUrl = null;
                        }
                    });
                },

                confirmSave() {
                    const self = this;

                    // Validasi minimal ada 1 produk
                    if (this.products.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Belum Ada Produk!',
                            text: 'Silakan tambahkan minimal 1 produk sebelum menyimpan.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Simpan Semua Produk?',
                        text: 'Pastikan semua data produk sudah benar.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            self.submitData(); // Panggil submit langsung
                        }
                    });
                },

                confirmCancel() {
                    const self = this;
                    Swal.fire({
                        title: 'Batalkan Semua Data Produk?',
                        text: 'Semua input yang sudah ditambahkan akan dihapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Batalkan',
                        cancelButtonText: 'Tidak Jadi',
                        confirmButtonColor: '#D22B2B',
                        cancelButtonColor: '#808080'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reset semua produk
                            self.products = [];

                            // Sembunyikan tombol batalkan dan simpan
                            document.getElementById('batalkan_btn').style.display = 'none';
                            document.getElementById('btn_save').style.display = 'none';
                        }
                    });
                },

                triggerFileInput(pIndex, cIndex) {
                    const inputs = this.$refs.fileInputs;
                    const refKey = `${pIndex}_${cIndex}`;

                    if (!inputs) {
                        console.error('Input file tidak ditemukan!');
                        return;
                    }

                    // Kalau cuma 1 input (bukan array)
                    if (inputs instanceof Element) {
                        if (inputs.dataset.ref === refKey) {
                            inputs.click();
                        }
                    } 
                    // Kalau multiple inputs
                    else if (NodeList.prototype.isPrototypeOf(inputs) || Array.isArray(inputs)) {
                        for (const input of inputs) {
                            if (input.dataset.ref === refKey) {
                                input.click();
                                break;
                            }
                        }
                    }
                },

                // Submit form: kirim JSON ke hidden input lalu submit form
                submitData() {
                    const payload = {
                        category: this.products[0]?.name || '',
                        name: this.products.flatMap((p) =>
                            p.categories.map((c) => ({
                                name: c.name,
                                desc: p.desc || ''
                            }))
                        )
                    };
                    this.$refs.products.value = JSON.stringify(payload);
                    this.$refs.form.submit();
                }
            }
            console.log('refs:', this.$refs);
            console.log('try access:', this.$refs[refName]);
        }
        // window.existingProducts = @json($produkList);
    </script>
    <div class="card overflow-x-auto">
        <div class="card-body ">
            <div class="card-title">
                <h4 class="card-title">{{__('Product List')}}</h4>
                {{-- Alpine.js scope --}}
               <form x-ref="form" method="POST" action="{{ route('store_prod') }}" enctype="multipart/form-data" 
                    x-data="productTable()" x-init="init()" @submit.prevent="submitData">
                    @csrf
                    {{-- Hidden input untuk simpan data JSON dari Alpine --}}
                    <input type="hidden" name="products" x-ref="products">
                    {{-- TABEL PRODUK --}}
                    {{-- <div class="overflow-x-auto"> --}}
                        <table class="table table-bordered table-auto border border-collapse w-full text-sm">
                            <thead class="bg-gray-200 text-gray-700">
                                <tr>
                                    <th class="border px-4 py-2 text-center">No</th>
                                    <th class="border px-4 py-2 text-center">Produk</th>
                                    <th class="border px-4 py-2 text-center">Deskripsi Produk</th>
                                    <th class="border px-4 py-2 text-center">Gambar Produk</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(product, index) in products" :key="index">
                                    <template x-for="(cat, cIndex) in product.categories" :key="cIndex">
                                    <tr>
                                        <!-- Nomor -->
                                            <td class="border px-4 py-2 text-center" x-text="index + 1" x-show="cIndex === 0" :rowspan="product.categories.length"></td>

                                        <!-- Nama Produk (Kategori) -->
                                        <td class="border px-4 py-2 align-top" x-show="cIndex === 0" :rowspan="product.categories.length">
                                            <input type="text" x-model="product.name" class="w-full border rounded p-1" placeholder="Kategori">
                                        </td>

                                        <!-- Nama Item -->
                                        <td class="border px-4 py-2">
                                            <input type="text" x-model="cat.name" class="w-full border rounded p-1" placeholder="Nama Produk">
                                        </td>

                                        <!-- Gambar -->
                                        <td class="border px-4 py-2 text-center">
                                            <template x-if="cat.imageUrl">
                                                <div>
                                                    <img :src="cat.imageUrl" class="w-16 h-16 object-cover border rounded">
                                                    <button type="button" @click="confirmDeleteImage(index, cIndex)" class="text-red-600 text-xs mt-1"><i class="ti-trash"></i></button>
                                                    <button type="button" @click="triggerFileInput(index, cIndex)" class="btn btn-primary btn-sm mt-1"><i class="ti-reload"></i></button>
                                                </div>
                                            </template>

                                            <template x-if="!cat.imageUrl">
                                                <div>
                                                    <button type="button" @click="triggerFileInput(index, cIndex)" class="btn btn-primary btn-sm">
                                                        <i class="ti-upload"></i>
                                                    </button>
                                                </div>
                                            </template>

                                            <!-- SELALU ADA INPUT FILE TERSEMBUNYI DI SINI -->
                                            <input 
                                                type="file" 
                                                :name="`images[${index}][${cIndex}]`" 
                                                @change="uploadImage($event, index, cIndex)" 
                                                accept="image/png, image/jpeg" 
                                                style="display: none;" 
                                                x-ref="fileInputs"
                                                :data-ref="`${index}_${cIndex}`"
                                            />
                                        </td>

                                        <!-- Aksi -->
                                        <td class="border px-4 py-2 text-center">
                                        <div class="space-x-1">
                                            <button type="button" @click="removeCategory(index, cIndex)" class="btn btn-danger btn-sm">x</button>
                                            <button type="button" @click="addCategory(index)" x-show="product.categories.length < 3" class="btn btn-success btn-sm">+</button>
                                            <div x-show="product.categories.length >= 3" class="text-xs text-red-500">Max 3</div>
                                        </div>
                                        </td>
                                    </tr>
                                    </template>
                                </template>
                                </tbody>
                        </table>
                    {{-- </div> --}}

                    {{-- Tombol Tambah Produk --}}
                    <div class="mt-4">
                            <button type="button" class="btn btn-success btn-md btn-icon-text disabled:opacity-50"
                                @click="addProduct()" :disabled="products.length >= 5">
                                <i class="ti-plus btn-icon-prepend"></i>
                                {{__('Tambah Produk')}}
                            </button>
                        <button type="button" class="btn btn-batalkan btn-md btn-icon-text" id="batalkan_btn" style="display: none"
                            @click="confirmCancel()">
                            <i class="ti-close btn-icon-prepend"></i>
                            {{__('Batalkan Tambah Produk')}}
                        </button>
                        <button type="button" class="btn btn-primary btn-md btn-icon-text" id="btn_save" @click="confirmSave()" style="display: none; float: right;">
                            <i class="ti-check btn-icon-prepend"></i>
                            {{__('Simpan Semua Produk')}}
                        </button>
                        <div x-show="products.length >= 5" class="text-danger mt-1 text-sm">
                            {{__('Anda Sudah Mencapai Maksimum Input (5 Produk)')}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>