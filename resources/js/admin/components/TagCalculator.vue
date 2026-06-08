<template>
    <div>
        <p class="small">
            Introduce dimensiones en centímetros. Separación entre etiquetas en milímetros (ej: 3 mm).
        </p>

        <div class="md:row">
            <div class="md:col-1/4">
                <div class="md:row">
                    <div class="md:col">
                        <section class="db-panel">
                            <h3 class="db-panel__title">
                                Área disponible
                            </h3>
                            <div class="form-control">
                                <label>Ancho (cm)</label>
                                <input class="form-field" v-model.number="areaW" type="number" step="any" />
                            </div>
                            <div class="form-control">
                                <label>alto (cm)</label>
                                <input class="form-field" v-model.number="areaH" type="number" step="any" />
                            </div>
                            <div class="form-control">
                                <input id="useAreaTotal" type="checkbox" v-model="useAreaTotal" />
                                <label class="small">
                                    Tratar como área por lado (ej. cuadrado 90×90). Marcar para ingresar área total (cm²)
                                </label>
                            </div>
                            <div v-show="useAreaTotal">
                                <label class="small">Área total (cm²)</label>
                                <div class="row" style="margin-top:6px">
                                    <input  class="form-field" v-model.number="areaTotal" type="number" step="any" />
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="md:col">
                        <section class="db-panel">
                            <h3 class="db-panel__title">
                            Etiqueta
                            </h3>

                            <div class="form-control">
                                <label>Ancho (cm)</label>
                                <input class="form-field" v-model.number="labelW" type="number" step="any" />
                                <span class="small"></span>
                            </div>
                            <div class="form-control">
                                <label>Alto (cm)</label>
                                <input class="form-field" v-model.number="labelH" type="number" step="any" />
                            </div>
                            <div class="form-control">
                                <label>separación (mm)</label>
                                <input class="form-field" v-model.number="gapMM" type="number" step="any" />
                            </div>  
                        </section>
                    </div>
                    <div class="md:col">
                        <section class="db-panel">
                            <h3 class="db-panel__title">
                                Precio del vinil
                            </h3>

                            <!-- Categoría, Producto, Acabado -->
                            <div class="md:row mb-4">
                                <div class="md:col-1/2">
                                    <div class="form-control">
                                        <label for="type_id">Categoría</label>
                                        <select class="form-field" name="type_id" v-model="fields.type_id">
                                            <option v-for="(type, key) in types" :key="key" :value="key">
                                                {{ type }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="md:col-1/2">
                                    <div class="form-control">
                                        <label for="product_id">Producto</label>
                                        <select class="form-field" name="product_id" v-model="fields.product_id" ref="searchSelect">
                                            <option v-for="(product, key) in filteredProducts" :key="key" :value="key">
                                                {{ product }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Dimensiones -->
                            <div class="md:row mb-2">
                                <div class="md:col-1/2">
                                    <div class="form-control">
                                        <label for="cut_id">Acabado</label>
                                        <select class="form-field" name="cut_id" v-model="fields.cut_id">
                                            <option v-for="(cut, key) in formattedCuts" :key="key" :value="key">
                                                {{ cut }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="md:col-1/2">
                                    <div class="form-control">
                                        <label for="quantity_product">Cantidad de material utilizado</label>
                                        <input class="form-field" v-model="fields.quantity_product" maxlength="80"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Precios -->
                            <div class="md:row mb-2">
                                <div class="md:col-1/2">
                                    <div class="form-control">
                                        <label for="base_price">Costo por unidad</label>
                                        <input class="form-field" v-model="fields.base_price" maxlength="80" 
                                        />
                                    </div>
                                </div>
                                <div class="md:col-1/2">
                                    <div class="form-control">
                                        <label for="profit_percentage">Utilidad por unidad</label>
                                        <input class="form-field" v-model="fields.profit_percentage" maxlength="6" />
                                    </div>
                                </div>
                            </div>

                            <!-- Importe -->
                            <div class="md:row mb-2">
                                <div class="md:col">
                                    <div class="sale-price-wrapper">
                                        <div class="sale-price-container">
                                            <label class="sale-price-label">Importe</label>
                                            <p class="sale-price-value">${{ salePrice.toFixed(4) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-control">
                                <label>Precio total ($)</label>
                                <input class="form-field" v-model.number="vinilPrice" step="0.0001" type="number" />
                            </div>
                        </section>
                    </div>
                </div>
                <div class="md.row">
                    <div class="col">
                        <section class="db-panel">
                            <h3 class="db-panel__title">
                                Calcular
                            </h3>
                            <button class="btn btn--success btn--wide" @click="calculate">Calcular y dibujar</button>
                            <div class="out" v-html="results"></div>
                            <div class="out" v-html="priceResult"></div>    
                        </section>
                    </div>
                </div>
            </div>
            <div class="md:col-3/4">
                <div class="md.row">
                    <div class="col">
                        <section class="db-panel">
                            <h3 class="db-panel__title">
                                Representación   
                            </h3>
                            <div class="small">Se muestra la mejor orientación (la que maximiza la cantidad).</div>
                            <div class="small">Canvas escalado a 700 px máximo</div>
                            <div style="margin-top:10px">
                                <canvas ref="canvas" width="700" height="700"></canvas>
                            </div>
                            <div style="margin-top:8px" class="flex">
                                <button class="btn btn--blue--dashboard btn--wide" @click="downloadCanvas">Descargar imagen</button>
                                <div class="small">{{ canvasInfo }}</div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EtiquetaCalculator",
    props: {
        products: { 
            type: [Object, Array], 
            required: true 
        },
        cuts: { 
            type: [Array, Object], 
            required: true 
        },
        types: { 
            type: [Array, Object], 
            required: true 
        },
    },
    data() {
        return {
            fields:{
                type_id: "",
                product_id: "",
                cut_id : "",
                quantity_product: "",
                base_price: "",
                profit_percentage: "",
            },
            areaW: 90,
            areaH: 90,
            areaTotal: 8100,
            useAreaTotal: false,
            labelW: 10,
            labelH: 5,
            gapMM: 3,
            vinilPrice: 0,
            results: "",
            priceResult: "",
            canvasInfo: "",
            formattedCuts:{}
        };
    },
    mounted() {
        
        this.$nextTick(() => {
            this.calculate();
        });
    },
    computed: {
        filteredProducts() {
            if (!this.fields.type_id) return {};
            return this.products
                .filter(p => p.type_id == this.fields.type_id)
                .reduce((obj, p) => {
                    obj[p.id] = p.name;
                    return obj;
                }, {});
        },

        salePrice() {
            const base = parseFloat(this.fields.base_price) || 0;
            const profit = parseFloat(this.fields.profit_percentage) || 0;
            return Number(base + (base * profit / 100));
        }
    },
    watch: {
        fields: {
            handler() {
                this.updateBasePrice();
            },
            deep: true
        },
        salePrice(newVal) {
            // Si el usuario no ha escrito nada, sincroniza
            this.vinilPrice = newVal;
        }
    },

    created() {
        this.formatCuts();
    },
    methods: {
        formatCuts() {
            this.formattedCuts = this.cuts.reduce((obj, cut) => {
                obj[cut.id] = cut.name;
                return obj;
            }, {});
        },

        updateBasePrice() {
            const productId = this.fields.product_id;
            const cutId = this.fields.cut_id;
            const quantity = parseFloat(this.fields.quantity_product) || 1;

            let basePrice = 0;

            const selectedProduct = this.products.find(p => p.id == productId);
            if (selectedProduct) {
                basePrice = (parseFloat(selectedProduct.costo_total) || 0) * quantity;
            }

            const selectedCut = this.cuts.find(c => c.id == cutId);
            if (selectedCut) {
                basePrice += (parseFloat(selectedCut.cost) || 0) * quantity;
            }

            this.fields.base_price = basePrice;
        },
        calc(W, H, w, h, s) {
            const nFila = Math.floor((W + s) / (w + s));
            const nCol = Math.floor((H + s) / (h + s));
            return { nFila, nCol, total: nFila * nCol };
        },
        drawGrid(W, H, w, h, s, cols, rows) {
            const pad = 10;
            const maxDim = 700 - pad * 2;
            const scale = Math.min(maxDim / W, maxDim / H);

            const canvas = this.$refs.canvas;
            const ctx = canvas.getContext("2d");
            canvas.width = Math.round(W * scale + pad * 2);
            canvas.height = Math.round(H * scale + pad * 2);
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.strokeStyle = "#bbb";
            ctx.lineWidth = 1;
            ctx.strokeRect(pad, pad, W * scale, H * scale);

            const cellW = w * scale;
            const cellH = h * scale;
            const gapPx = s * scale;

            ctx.fillStyle = "#e6f0ff";
            ctx.strokeStyle = "#2b6cff";
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    const x = pad + c * (cellW + gapPx);
                    const y = pad + r * (cellH + gapPx);
                    if (
                        x + cellW <= pad + W * scale + 0.0001 &&
                        y + cellH <= pad + H * scale + 0.0001
                    ) {
                        ctx.fillRect(x, y, cellW, cellH);
                        ctx.strokeRect(x + 0.5, y + 0.5, cellW - 1, cellH - 1);
                    }
                }
            }

            ctx.fillStyle = "#333";
            ctx.font = "13px sans-serif";
            ctx.fillText(
                cols + " × " + rows + " = " + cols * rows + " etiquetas",
                12,
                canvas.height - 10
            );

            this.canvasInfo = `Escala: 1 px = ${(1 / scale).toFixed(2)} cm (aprox)`;
        },
        calculate() {
            let W = parseFloat(this.areaW);
            let H = parseFloat(this.areaH);

            if (this.useAreaTotal && this.areaTotal > 0) {
                const side = Math.sqrt(this.areaTotal);
                W = H = side;
            }

            const w = parseFloat(this.labelW);
            const h = parseFloat(this.labelH);
            const gapMM = parseFloat(this.gapMM);
            const vinilPrice = parseFloat(this.vinilPrice);

            if (isNaN(W) || isNaN(H) || isNaN(w) || isNaN(h) || isNaN(gapMM)) {
                this.results =
                '<div style="color:#c00">Por favor llena todos los campos numéricos.</div>';
                return;
            }

            const s = gapMM / 10;

            const normal = this.calc(W, H, w, h, s);
            const rotada = this.calc(W, H, h, w, s);

            const best =
            normal.total >= rotada.total
            ? { mode: "normal", data: normal }
            : { mode: "rotada", data: rotada };

            this.results = `
                <div class="small">Orientación normal (w × h): ${normal.nFila} × ${normal.nCol} = <strong>${normal.total}</strong></div>
                <div class="small">Orientación rotada (h × w): ${rotada.nFila} × ${rotada.nCol} = <strong>${rotada.total}</strong></div>
                <div style="margin-top:8px"><strong>Mejor orientación:</strong> ${best.mode} → <strong>${best.data.total}</strong> etiquetas</div>
            `;

            if (vinilPrice > 0 && best.data.total > 0) {
                const unitPrice = vinilPrice / best.data.total;
                this.priceResult = `<div style="margin-top:8px">Precio unitario: <strong>$${unitPrice.toFixed(
                    2
                    )}</strong></div>`;
            } else {
                this.priceResult = "";
            }

            if (best.mode === "normal") {
                this.drawGrid(W, H, w, h, s, best.data.nFila, best.data.nCol);
            } else {
                this.drawGrid(W, H, h, w, s, best.data.nFila, best.data.nCol);
            }
        },
        downloadCanvas() {
            const canvas = this.$refs.canvas;
            const url = canvas.toDataURL("image/png");
            const a = document.createElement("a");
            a.href = url;
            a.download = "etiquetas.png";
            a.click();
        },
    },
    mounted() {
        this.calculate();
    },
};
</script>

<style scoped>
    :root {
        --gap: 10px;
    }
    h1 {
        font-size: 20px;
        margin: 0 0 8px;
    }

    label {
        font-size: 13px;
    }

    .out {
        margin-top: 10px;
    }
    canvas {
        width: 100%;
        height: auto;
        border: 1px solid #ddd;
        background: white;
        border-radius: 6px;
    }
    .small {
        font-size: 13px;
        color: #444;
    }
    footer {
        margin-top: 12px;
        color: #666;
        font-size: 13px;
    }
    .flex {
        display: flex;
        gap: 10px;
        align-items: center;
    }
</style>
