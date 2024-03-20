<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { Container, Draggable } from "vue-dndrop";
import { usePermissions } from '@/composable/permission';
import Knob from 'primevue/knob';
import FullCalendar from '@/Components/FullCalendar.vue'
import Loading from '@/Components/Loading.vue';
import CustomInput from '@/Components/CustomInput.vue';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Listbox from 'primevue/listbox';
import CustomModal from '@/Components/CustomModal.vue';
import RadioButton from 'primevue/radiobutton';
import Calendar from 'primevue/calendar';
import CustomShiftSelector from '@/Components/CustomShiftSelector.vue';
import ButtonGroup from 'primevue/buttongroup';
import Breadcrumb from 'primevue/breadcrumb';
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import InputSwitch from 'primevue/inputswitch';
import { Link } from '@inertiajs/vue3'


// El código anterior es una función llamada `format24h` que toma un parámetro `hora` (que representa
// una hora en formato de 24 horas) y devuelve una cadena de hora formateada en formato de 12 horas con
// AM/PM.
function format24h(hora) {
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}


//#endregion

</script>

<template>
    <AppLayout>
        <div class="h-full w-full">
            <div class="flex justify-between items-center px-4 h-min">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    <p> Programación de Actividades </p>
                    <!-- <CustomInput type="week" v-model:input="date" @change="getTask('date'); getPersonalData()" /> -->
                </span>
                <div class="flex items-center space-x-2">
                    <Link :href="route('programming.create')">
                    <Button label="Programar Personal" severity="success" icon="fa-solid fa-plus" />
                    </Link>
                </div>
            </div>
            <div class="mt-2 px-4 h-[79vh] overflow-y-auto">
                Lorem ipsum dolor sit amet consectetur, adipisicing elit. Similique, corporis doloribus mollitia odio
                eius quidem vel eligendi impedit, nostrum iure doloremque atque ad quis. Eius ullam eveniet pariatur
                fugit accusamus.
                Alias odit praesentium illum perspiciatis illo non eius exercitationem aliquid aliquam quaerat. Est,
                recusandae! Dignissimos, impedit voluptas? Dolorum, quas. Corporis, quam iste ipsam non similique maxime
                nisi porro velit nobis!
                Excepturi voluptates iusto aliquid, tenetur sit illo sed atque totam accusamus officia tempore. Dolores
                veniam eaque nostrum iure quos voluptas quia. Maxime placeat autem reiciendis, laborum error cum ex
                quaerat!
                Impedit laboriosam repellendus cupiditate ex voluptate blanditiis at eum placeat enim molestiae cum
                reprehenderit, nihil delectus, dolore veniam! Asperiores officiis odit deserunt eum minus facilis rem
                nemo illum placeat earum.
                Et dolor dolores exercitationem temporibus debitis dolore error velit officiis. Repellendus odit,
                explicabo culpa quas sint autem excepturi ducimus vel necessitatibus cupiditate, magni quod nulla quam
                dolore doloremque aliquid est!
                Nihil impedit, magni, dolor officiis voluptatem aut quisquam odio maiores dicta explicabo sint ad
                dignissimos, repudiandae dolores quam eligendi ea totam id! Fugit itaque, quos quia magnam architecto
                ducimus? Nostrum.
                Error, consectetur quis doloribus nulla dolorem quasi autem beatae perferendis rem, possimus adipisci,
                sapiente ex! Magni atque assumenda, dolor velit delectus doloremque tenetur facilis at doloribus, ut
                officia reprehenderit eveniet!
                Veritatis unde similique culpa fuga ut quam ad pariatur assumenda atque nisi, ipsam quos quas! Totam
                illo provident eaque neque libero consequuntur qui molestias, placeat odio vitae ea quam pariatur?
                Repellat, deleniti est dolore, velit ipsum quam sit ullam fugit esse cum tempora ex amet consequatur
                iste inventore voluptate quibusdam? Fuga neque nobis voluptate officia quisquam explicabo quam,
                aspernatur minus.
                Architecto dignissimos facilis et repudiandae magni, ea modi inventore eveniet consequuntur unde veniam
                hic in tempore, amet, iusto quaerat sit maxime reiciendis temporibus ratione asperiores recusandae
                itaque. Repellendus, hic culpa.
                Autem quia illum perspiciatis repellendus voluptatibus. Inventore voluptatibus labore ea in suscipit
                excepturi rem, voluptates natus, amet doloremque at quo ducimus blanditiis minus alias reprehenderit
                eaque ex cumque repudiandae cupiditate.
                Quis quibusdam, maxime unde inventore debitis repellat tenetur doloribus quia vitae perferendis ab
                laudantium magnam fugiat cumque deleniti aliquid omnis voluptatem. Nesciunt architecto quia soluta
                praesentium consequuntur aspernatur eum molestias?
                Accusamus, adipisci? Deserunt iure dolorem adipisci, magnam cumque atque eius! Recusandae, doloribus
                quidem iure consectetur non maxime quo veniam, hic aliquid quod sit neque nobis fugiat sapiente. Sequi,
                voluptas labore!
                Molestias accusamus sapiente, deleniti quod cum rerum placeat unde, saepe doloremque perspiciatis, velit
                reprehenderit ab! Alias natus provident dolor repellendus, rerum sapiente, neque cumque sequi ab qui
                numquam. Aut, earum.
                Dicta necessitatibus modi ipsum adipisci sed eligendi dolores? At tempore vel numquam consectetur quae
                velit adipisci expedita culpa iste laboriosam obcaecati, hic minima, dicta placeat dolorem suscipit
                magni quam facere.
                Facilis nam ad culpa distinctio mollitia facere commodi dolore voluptatum, et illo sint pariatur
                cupiditate dignissimos officiis quisquam explicabo quidem perferendis placeat ut totam tempora sapiente
                neque voluptatem natus. Delectus?
                Veritatis, nulla dicta. Perspiciatis impedit placeat est veritatis, expedita molestiae ea suscipit in
                optio quaerat! Optio sed at doloremque veniam magnam, minus distinctio culpa et cupiditate, maiores
                dolore eveniet a.
                Quos eligendi quia sequi. Iure suscipit commodi deserunt quisquam, libero cum aspernatur reprehenderit
                velit sed? Quasi, quod natus dolore deleniti quo repudiandae doloribus molestias possimus, aliquid eum
                eveniet vitae ullam.
                Perspiciatis quas dolore autem cumque error, doloremque quisquam, sapiente officiis ab voluptatem saepe
                quasi accusantium dicta tenetur quam facere, commodi provident! Dolor sed adipisci animi nisi temporibus
                impedit fuga quidem!
                Expedita molestiae, officia ipsum ducimus, unde deserunt illum provident, quasi placeat veniam
                praesentium culpa. Hic, non. Assumenda placeat inventore quas explicabo sit quae rerum nostrum
                asperiores, corrupti repellendus. Molestias, possimus!
                Consectetur dolorum quidem inventore nihil fugiat dolor illum veritatis odit culpa cum exercitationem
                accusantium labore soluta eaque quis vel atque minus, ut cumque ipsum maxime! Earum nisi adipisci enim
                cum!
                Ut, rem! Perspiciatis aliquid et facilis quasi esse dolores animi tempora ratione excepturi enim. Atque,
                tenetur esse veniam error blanditiis sint, ducimus a veritatis, vero temporibus nulla repellendus.
                Velit, culpa.
                Possimus odio ipsam iusto a veniam sapiente repudiandae reprehenderit, laborum atque quam praesentium
                repellendus vero pariatur commodi perspiciatis id corrupti ipsa nam quas suscipit nisi earum. Eaque
                nostrum minus voluptas.
                Sequi ducimus fugit ut quia itaque culpa sit ab nemo deleniti dolore. Placeat voluptates odio ipsam
                aliquid ipsa, exercitationem vel voluptatibus, alias voluptatum totam quisquam suscipit? A deleniti
                placeat similique!
                Est harum hic quis veritatis dolores, iure dolorem. Illum eius sit in magni facere sequi ex alias saepe
                quidem tempora laborum a, quod excepturi optio. Quo vitae debitis cum corrupti!
                Reiciendis voluptatum laudantium minima. Perspiciatis numquam perferendis distinctio libero voluptates,
                laudantium, qui mollitia officiis doloremque optio odit! Esse accusamus unde debitis reiciendis
                consectetur expedita? Excepturi magni vitae repudiandae adipisci fuga!
                Perferendis corporis reprehenderit itaque, quidem nobis qui, quisquam fugiat est quod ullam cumque natus
                sed quaerat magni, voluptatibus distinctio exercitationem voluptatem iste fuga asperiores vero minima
                cupiditate? Molestias, aliquam omnis?
                Fugiat pariatur maiores, explicabo voluptates accusamus assumenda molestias voluptas sequi facilis
                illum? Recusandae corrupti ullam quae minus provident repellendus tempore error molestiae sapiente quis.
                Maiores, architecto. Quod quaerat neque architecto?
                Eum quod libero quas minus eveniet quae expedita aspernatur suscipit ratione numquam, repudiandae labore
                odio quam enim, nam pariatur, tenetur odit nesciunt debitis explicabo saepe consectetur aperiam.
                Provident, rerum nemo?
                Voluptates deserunt asperiores vitae cum, odio ad voluptatum corporis ducimus dolorem, quibusdam
                suscipit corrupti. Soluta totam incidunt quasi labore sunt dolore, ratione vitae iusto consequuntur vero
                quaerat eaque praesentium magni.
                Commodi perferendis ea odit sunt, fugiat magni quis, tempore adipisci iusto qui nam repellendus nemo non
                ullam voluptatem nihil vero pariatur saepe maxime ipsum cumque? Repudiandae dolore aliquid excepturi in.
                Et error pariatur id atque impedit ut suscipit vero libero possimus distinctio placeat, accusantium vel
                rerum repellat. Labore praesentium porro vitae vero! Nesciunt praesentium, quia eum mollitia
                voluptatibus iure quisquam?
                Voluptates ad id, culpa minus totam facere vitae sint sunt sit accusamus harum molestiae numquam fugit,
                quibusdam voluptatem dolores voluptate eligendi repellendus ut accusantium corporis? Commodi provident
                corporis omnis eveniet?
                Ratione asperiores dolorum odit ducimus, quia velit rerum similique corrupti veniam esse id, illo
                inventore mollitia vitae tenetur illum! Eveniet facilis assumenda distinctio iste dolore doloribus
                necessitatibus ex odio perferendis?
                Labore nostrum consectetur et, non consequuntur quam quibusdam laboriosam totam fugit sed, earum impedit
                corrupti debitis tenetur temporibus nesciunt nemo dolore optio similique, modi nihil? Voluptas ipsum
                corrupti perspiciatis repudiandae.
                Atque excepturi quam sunt ipsam, ipsum nihil quo id! Sequi facere officiis tempora dolorum quasi totam
                laboriosam sint tempore eligendi esse! Perspiciatis magnam dolorem rem debitis blanditiis ad velit
                mollitia?
                Placeat sit non reprehenderit veritatis quo ratione? Ullam quaerat voluptas quidem voluptatem, mollitia
                enim, maxime totam nisi officiis dolorem architecto id vitae minus labore velit exercitationem, illum
                atque ab impedit.
                Necessitatibus iste voluptates quod illo deleniti sed eos error aperiam alias consequuntur. Architecto
                nobis minus sunt nesciunt temporibus sequi fugit dicta perferendis eum eveniet, sit veniam aut error,
                numquam ad!
                Architecto totam vitae quibusdam magnam rerum natus excepturi magni, harum ad beatae perferendis tenetur
                impedit officia eos recusandae earum. Magnam voluptatum, tempora quod recusandae perferendis hic
                molestiae debitis? Voluptas, deserunt!
                Voluptatem molestias nemo esse ex magnam non, nam labore iusto doloremque vel ullam aliquid eveniet
                mollitia rem quis? Perferendis praesentium enim sint maiores tempora eveniet vel quam rerum aliquid!
                Libero!
                Consequatur fugit laboriosam fugiat eos molestias reprehenderit assumenda laborum incidunt officia,
                magnam aliquam voluptatum voluptatem alias voluptatibus blanditiis illum provident error explicabo.
                Amet, maiores mollitia sit perspiciatis et error consectetur!
                Totam aliquam delectus enim molestias ducimus quos odio harum praesentium nemo vero non atque culpa
                corporis, voluptatibus reprehenderit dolores. Rerum impedit perspiciatis suscipit corrupti saepe
                perferendis laboriosam cupiditate, deleniti repellendus.
                Voluptates eaque architecto dolorum reprehenderit, fugit excepturi veritatis quaerat corrupti. At ipsum,
                ipsa, suscipit magni laudantium alias quos ex enim cum exercitationem minima eligendi expedita? Officiis
                nihil iste incidunt consequuntur?
                Delectus minus quis rem! Deleniti eos aspernatur atque vitae dolorum ducimus in, sequi eaque omnis
                dolore aperiam aliquam dignissimos voluptatem. Ducimus commodi fuga eligendi eius in. Doloribus quia
                mollitia accusantium?
                Ex, provident? Esse qui dolores quod excepturi quidem, beatae et porro corrupti perspiciatis quas
                libero. Aperiam quas est cupiditate quisquam incidunt expedita, placeat, accusantium nulla, facere
                quidem tempore ratione dignissimos?
                Itaque laborum rerum aspernatur tempora saepe non inventore quos accusamus eius obcaecati, modi nulla,
                quasi fugit veritatis libero in quas quo. Porro assumenda blanditiis laudantium vitae, perspiciatis sint
                corrupti nihil.
                Beatae suscipit repellendus eligendi dolorem, voluptatem reiciendis est repudiandae saepe quae, illum
                expedita? Autem qui ad dignissimos aliquam dolorum, optio fugit, harum culpa rerum voluptates impedit.
                At voluptatem ut ratione?
                Unde, veritatis doloremque quas dicta, alias harum optio repellendus rerum doloribus atque, ad fugit?
                Quae ratione excepturi sit corporis inventore accusamus nam quis mollitia aspernatur sint, earum sequi
                eius atque.
                Soluta, eos rerum amet, possimus ea facilis quas illo atque illum exercitationem optio sit dolorum
                quaerat odit molestias necessitatibus maxime, aut animi a repellendus. Sunt velit nostrum maiores rem
                consequuntur?
                Cum consequuntur harum obcaecati architecto delectus, asperiores possimus non placeat accusantium,
                dolorum illum quasi voluptate deleniti amet, enim officiis id adipisci. Aut iusto iste obcaecati, non
                deserunt reiciendis vero esse?
                Doloremque itaque saepe officia illum. Dolor, excepturi cupiditate tempore repellat ratione ea vero
                assumenda voluptatum ipsum aspernatur accusamus tempora eaque earum maiores voluptates molestiae
                reiciendis, nam, quis ad? Temporibus, dolore.
                Quia quidem libero quae labore natus cupiditate, mollitia iusto, velit similique tempore optio
                consequuntur dolorum deserunt alias unde ex nemo earum corrupti corporis non error? Magni voluptates
                quam vitae inventore.
                Unde, aspernatur. Perferendis dolores, reiciendis quam ad quo voluptatibus ut aliquid. Dolor aperiam
                accusamus reprehenderit, mollitia, aliquid nesciunt in perspiciatis numquam aliquam amet ea, explicabo
                voluptas cupiditate eligendi optio est.
                Ipsam facere ab magnam ullam aut, nam blanditiis deleniti numquam repudiandae sint ipsum soluta quidem.
                Porro, natus itaque nihil odit nulla voluptatibus possimus distinctio autem similique, quo repellendus
                non sint.
                Saepe rerum facilis quasi eligendi soluta corporis natus odit facere possimus sequi nam modi quam odio
                vitae quas fuga magnam, error beatae ipsum nemo nobis, officia voluptates assumenda pariatur. Provident?
                Quae tempora, laudantium recusandae aperiam sequi voluptatum quisquam quas eum ducimus. Iusto, sequi!
                Facere, repellendus a veniam magnam fuga dignissimos voluptas dolor totam explicabo pariatur illum alias
                neque illo autem.
                Accusamus nobis non ex totam corrupti praesentium harum dolore neque sequi ipsam eligendi, provident ab
                tempore tenetur id vel! Ratione, fuga eos consectetur dolores explicabo asperiores vitae praesentium.
                Soluta, aut.
                A est, quas, dignissimos libero nemo, impedit similique aut delectus laborum non perspiciatis voluptatem
                architecto? Aliquid velit rem odit officia, reprehenderit, voluptas neque nihil ipsa cum accusantium
                vitae quas mollitia!
                Voluptate soluta explicabo dolorum enim facilis saepe, hic repellendus dicta reprehenderit fugiat minus
                nesciunt, iste dolor cumque magnam accusamus exercitationem voluptatibus et porro fuga ullam libero.
                Sint qui non nam!
                Reprehenderit perferendis eveniet sed ullam, illum ipsa quibusdam ratione quis illo alias dolores totam
                ea, corrupti, porro ipsum aliquid voluptatem nobis libero? Dignissimos asperiores ducimus ipsum illo
                dolorem mollitia nam?
                Voluptatibus excepturi provident hic nostrum sed est adipisci quasi natus exercitationem veritatis
                voluptate iusto, sunt repellendus? Nobis, atque fugit quibusdam ex, ut fugiat amet culpa perspiciatis et
                cumque pariatur adipisci.
                Repudiandae sit doloremque amet laudantium obcaecati aspernatur laborum quaerat exercitationem cumque
                culpa quod molestiae, reiciendis voluptatibus nobis est ipsa totam ea non at voluptate sint alias illo
                eaque provident. Animi.
                Quos animi saepe tempore aspernatur, corrupti, magni et quod quidem quaerat porro cupiditate dolores.
                Corrupti et molestiae delectus inventore, possimus pariatur accusantium, tempore nisi non commodi
                facilis quod laboriosam autem.
                Distinctio sequi omnis voluptas at quo! Officia, nobis deserunt earum, cumque delectus, laboriosam sequi
                enim officiis quaerat voluptatem assumenda odio provident nemo id. Nobis eius ullam, porro eum delectus
                ea!
                Aspernatur quo vero debitis similique voluptate minus, ipsa earum beatae soluta? Pariatur magni aut
                consequuntur rem velit nisi praesentium. Minus deleniti voluptatibus sequi est cumque ea numquam animi
                temporibus enim!
                Nihil minus tempora laudantium dolorem ullam hic impedit rerum aperiam saepe sunt, consequuntur,
                pariatur quo, inventore odit odio modi corporis mollitia quis repellendus dolores tempore in ipsam esse.
                Voluptas, eum!
                Impedit numquam dolorem necessitatibus repudiandae nihil autem sed tempora tempore ut molestiae deleniti
                soluta nisi fugit velit optio eum vel corrupti, aliquam possimus quod amet sunt ex! Quas, voluptas quis.
                Ducimus molestiae numquam at eum delectus illum sint tempore tempora veritatis, corporis asperiores et
                inventore blanditiis necessitatibus, repudiandae sequi sunt. Sint consequuntur voluptates deleniti
                possimus molestias at accusantium. Quia, labore!
                Ipsa autem molestiae totam iste officia! Fugit molestias sequi qui dolorum praesentium mollitia in
                quidem totam, similique blanditiis enim repudiandae consequatur nemo laudantium? Sint nemo animi velit
                dolor non harum?
                Magnam laboriosam consectetur inventore deserunt, aspernatur laborum assumenda labore necessitatibus
                adipisci perferendis minus sunt nostrum odio accusantium autem quos molestiae totam. Veniam blanditiis
                ipsa fugit necessitatibus assumenda temporibus rerum quis!
                Veniam iste molestias voluptatem ea. Impedit culpa, voluptatibus, magnam distinctio doloremque similique
                facere explicabo asperiores rerum consectetur illum totam aliquid deserunt provident. Voluptate culpa,
                adipisci fugit esse sapiente saepe fuga?
                Eius magni reprehenderit est at quidem voluptates rem velit quam inventore. Aut tempore quis culpa
                cumque blanditiis accusantium aspernatur dicta numquam? Quo saepe odit mollitia est eos quia, rerum
                voluptas.
                Aliquid aut dolor doloribus consectetur cumque provident pariatur tempore nemo eaque quisquam maiores ut
                fuga, animi sint eius ab delectus inventore consequuntur, non error quaerat! Aspernatur molestias dicta
                hic pariatur.
                Voluptatibus aut quas ad dolores sit dolorem molestiae fugiat quod. Deleniti ut dolore corrupti quia rem
                sunt provident quam nesciunt, commodi unde veniam, accusamus adipisci culpa enim illo vero in?
                Illum saepe ut fuga inventore. Dicta obcaecati, earum natus ut tempora maiores veritatis quis tenetur
                itaque nisi et iure ipsa nulla repellendus ipsum? Reiciendis accusamus earum veniam neque officiis aut!
                Porro possimus quidem animi natus optio voluptate pariatur impedit neque, soluta deserunt asperiores
                magnam minima labore consequatur, aliquam officia dolor necessitatibus ea corrupti iusto provident hic
                saepe. Accusantium, delectus explicabo.
                Nesciunt reiciendis expedita laudantium temporibus architecto ipsa id sit hic inventore voluptates vitae
                odio sunt nobis, dignissimos natus iste dolore qui, sed saepe, magnam fugiat praesentium labore soluta?
                Delectus, voluptas.
                Veniam nihil dolor vitae asperiores quia odio debitis inventore fuga. Neque rerum numquam beatae quasi
                officia enim eos, sequi, id cum, nam velit? Aperiam ad sint quidem nemo optio! Corrupti?
                Nobis perspiciatis nam ipsa minima mollitia reiciendis perferendis facere maxime libero non veritatis
                ipsum tempora in dolorem enim distinctio, quaerat, illo error tenetur magni eveniet voluptatem? Modi
                soluta natus tempora.
                Obcaecati magnam dolore ut distinctio, reprehenderit optio aliquam. Ipsa optio delectus porro officia,
                voluptas nostrum adipisci ut quisquam id, iste amet officiis rerum inventore accusantium sint, explicabo
                repellendus maxime blanditiis.
                Harum distinctio doloremque voluptate perspiciatis, ad aliquid error facilis repellat quam autem earum
                accusantium est, necessitatibus perferendis ea inventore atque nisi ab odit deserunt cupiditate
                voluptatibus laboriosam fugiat? Asperiores, vel.
                Id est repellendus aspernatur eius eligendi molestiae tempora totam quas odio fuga mollitia vel
                consectetur natus aut, esse dignissimos perferendis nemo minus numquam omnis ad ullam laboriosam
                consequatur. Itaque, quo!
                Odit illum alias dolores, tempore placeat praesentium natus aspernatur totam ad unde aliquid incidunt
                accusantium accusamus labore esse, quas nihil blanditiis voluptatum quia id commodi impedit! Cum maiores
                pariatur veritatis?
                Aut alias minus, autem ipsam nihil eum eos inventore ab labore ea temporibus non quos excepturi aliquid
                natus dolor et magnam quae sit, earum qui! Nesciunt dolores quas officiis illum?
                In voluptatum dolorem ullam minus molestias laborum deleniti neque pariatur ut, provident quia vitae eum
                nostrum sunt sed assumenda? Earum perferendis explicabo sed. Distinctio eligendi expedita pariatur.
                Corporis, deserunt. Placeat!
                Adipisci unde delectus labore quis molestiae libero placeat vero ratione cum quidem iste aut corrupti
                nostrum natus quibusdam, ad dignissimos at, quas quisquam dicta fugit accusantium sit neque. Maiores,
                deleniti.
                Molestiae, tenetur adipisci autem numquam suscipit cumque placeat iure illum et odit alias hic modi
                neque id rerum quidem accusamus dolor explicabo labore rem deleniti delectus minus molestias! Sint,
                exercitationem.
                Quo quae mollitia eligendi aut dolore distinctio fugit architecto veritatis nemo praesentium quaerat
                aspernatur perspiciatis fuga ducimus, cupiditate laborum sit a deleniti magnam iure odit asperiores
                suscipit vel quibusdam! Eos!
                Libero delectus velit vero corrupti ipsa minima quaerat inventore ab hic nobis ut cumque et blanditiis
                quod asperiores ullam, praesentium provident culpa amet. Quidem hic fugit assumenda exercitationem nam
                cupiditate!
                Exercitationem deleniti officia ipsum, distinctio vitae repudiandae, quo, possimus illum veniam
                voluptates iste magni. Voluptate enim eligendi doloremque vitae ratione, tempora quae ipsam fuga
                temporibus! Quisquam libero numquam error voluptatum?
                Laudantium veritatis et dolores fugiat quibusdam quisquam repellendus accusamus. Voluptas hic assumenda
                necessitatibus est saepe soluta maiores consequuntur vero veritatis debitis! Quae, hic? Soluta,
                dignissimos delectus harum accusamus ut tenetur?
                Sapiente sed inventore a voluptatibus recusandae incidunt obcaecati laudantium temporibus mollitia
                adipisci, numquam laboriosam veniam necessitatibus voluptatum beatae ducimus neque doloribus voluptas
                eligendi suscipit nisi reprehenderit ipsam magnam dolorum? Esse!
                Nobis molestias debitis, nemo excepturi deserunt possimus dolore sapiente asperiores nihil eos quibusdam
                dolores sunt aut in iste eum, nesciunt delectus! Consequuntur optio dolore iure provident esse quasi,
                consectetur voluptatibus?
                Nemo excepturi sint odio? Iure in tempora voluptas quas impedit, ut, similique molestiae aut accusamus
                reprehenderit iusto ad minima. Error, vero tempore! Illo numquam asperiores architecto quibusdam tempore
                quidem pariatur!
                Voluptas, eos voluptates eius reiciendis corrupti rem, quisquam a dolore consequuntur reprehenderit unde
                deleniti consectetur tenetur iusto ullam! Ipsum officia, adipisci sapiente cupiditate soluta
                perspiciatis qui ut sunt consequatur dolorum.
                Temporibus assumenda hic consectetur minima suscipit dolores neque, natus ipsum eos exercitationem
                consequatur voluptatibus, accusantium, facilis eligendi iusto tempora expedita maiores nisi sint
                laudantium quas ex. Qui perspiciatis vitae nostrum?
                Labore praesentium pariatur porro voluptas blanditiis accusantium tempora? At ducimus ea cupiditate
                molestiae possimus totam rem quas excepturi recusandae quisquam accusantium expedita vitae dolore
                voluptates fugit amet, tenetur numquam qui.
                Deserunt quae voluptate dolorum repellat adipisci nam similique quo velit! Magni neque quo suscipit
                asperiores tempora soluta natus nobis ipsa fugit illum delectus, obcaecati facere distinctio. Ducimus
                consequatur non perferendis!
                Dicta veritatis voluptatem reiciendis natus tenetur quibusdam vitae ullam deleniti recusandae nesciunt
                placeat soluta, nobis nisi harum aut cupiditate alias, a nam quis. Rerum cum, natus magni impedit
                aliquam culpa?
                Earum repudiandae reprehenderit iure, asperiores blanditiis suscipit. Molestiae atque iste blanditiis,
                illum nesciunt amet nihil aliquam quidem pariatur, doloremque, quae nisi nulla! Sit, voluptatibus fuga
                quia numquam asperiores culpa non!
                Cupiditate repudiandae iure ex unde, ipsa in. Asperiores aliquam, ex, recusandae in delectus fugit dolor
                esse voluptatem atque sequi modi iure dignissimos ab culpa vero praesentium, similique dolores placeat
                sit?
                Ducimus fuga dicta, repudiandae aliquam molestias nostrum expedita a non suscipit nobis excepturi
                blanditiis, dolorem distinctio temporibus vel perspiciatis deleniti velit recusandae! Sapiente debitis
                consectetur dignissimos fugiat labore voluptas ratione!
                Magni dolor quo molestias consectetur magnam alias? Sed doloremque voluptatibus quis, optio fugit nihil
                officiis unde voluptas temporibus sit rerum ut ab ducimus vitae perferendis. Pariatur facilis repellat
                unde omnis!
                Magni iusto laboriosam dolores mollitia libero, a saepe incidunt nihil ipsa temporibus excepturi quasi
                voluptatem blanditiis officia odit repellat dignissimos quo necessitatibus laborum fuga cumque sunt quae
                corrupti. Nulla, sit.
                Perferendis, inventore non? Vitae voluptate ea illo, modi corrupti itaque provident dolorem porro! Odit,
                quo. Distinctio sunt nihil numquam, saepe odio commodi praesentium fuga, ab repellendus tempore
                voluptate voluptatibus ad?
                Eius nostrum, aliquam voluptatum corporis voluptatibus excepturi aspernatur ab quis doloribus assumenda
                enim eligendi delectus ea deserunt porro repellat neque, reiciendis accusamus incidunt! Id deleniti
                esse, mollitia vero tempore recusandae.
                Sit delectus ipsa alias dolor, veritatis dolores recusandae iure vero itaque reprehenderit hic
                consectetur numquam enim explicabo quia magni quae doloremque minus quo obcaecati possimus aperiam saepe
                est. Amet, labore.
                Veritatis, aliquid porro itaque, doloribus facilis omnis ratione sapiente, dolorum repellendus nemo odit
                cupiditate praesentium consequatur ipsa voluptatum blanditiis? Esse distinctio voluptate illo. Sed nemo
                sit eum deserunt, earum nobis?
                Doloribus nulla ex aperiam eius voluptatum ab, optio quasi quaerat iusto molestiae commodi delectus odio
                repellat amet? Nisi beatae ipsa harum error, nulla atque velit, doloremque perferendis, nemo accusantium
                ab!
                Exercitationem vitae, iste esse doloremque quibusdam itaque eligendi voluptatem expedita accusantium
                minima sit aliquid, id ab officia aperiam neque distinctio incidunt temporibus voluptatibus! Recusandae
                ex nam alias corporis. Odio, esse?
                Nulla aliquid pariatur necessitatibus ipsa sunt provident laudantium quas? Quam voluptatem illum iste
                alias nisi saepe perspiciatis officiis accusamus, odit neque eum accusantium odio hic. Quisquam
                repudiandae exercitationem aliquid quam.
                Alias, vitae. Beatae ratione similique iste reiciendis laudantium qui atque, velit neque tenetur eaque
                ea corrupti rem doloribus earum ex eligendi! Soluta mollitia neque dolore maiores repudiandae earum
                libero perferendis.
                Tempore quod libero exercitationem debitis, quia minima enim veritatis iure. Doloribus, esse recusandae
                quis quia placeat odio ipsam magnam? Voluptatum dolorum aliquid minima consequatur. Temporibus unde
                recusandae suscipit cumque doloribus?
                Odit rem eos minima iure voluptatem reprehenderit, repellendus sit quibusdam dolore. Pariatur obcaecati
                sed quidem mollitia, beatae nostrum laudantium minus quis reiciendis, fugiat esse deleniti. Pariatur
                maiores ipsam sit eum!
                Magnam similique repudiandae modi incidunt illum distinctio doloribus! Necessitatibus at perferendis
                quisquam optio fugiat? Dolore non consequuntur ipsam est adipisci assumenda, quas quo enim at a! Aperiam
                exercitationem sequi sint!
                Veritatis expedita reprehenderit dolore, magni consectetur nostrum iste ab. Sint nam atque facilis autem
                itaque, officia eaque alias distinctio numquam corrupti minus porro dolorem soluta! Voluptas nobis
                dignissimos ipsa sed!
                Fuga mollitia ipsum fugiat nemo consectetur doloremque et, fugit possimus dolor minus quibusdam cumque,
                non, pariatur accusantium. Praesentium quae dolore voluptate voluptas a inventore corporis, asperiores
                dolor, necessitatibus fuga esse.
                Repellat voluptate ipsam quisquam, accusantium deleniti eaque debitis sequi possimus molestias. Voluptas
                deleniti qui inventore libero obcaecati pariatur illo aperiam sint rem esse quisquam, cumque deserunt
                commodi hic accusamus nostrum!
                Reprehenderit dolor, iste sed sit hic officia. Repudiandae repellendus unde deleniti explicabo? Ut
                facere est sint nisi sed ea nam vitae, in fugiat. Reiciendis aperiam blanditiis, quibusdam optio ipsam
                enim.
                Fuga tempora dolore quos mollitia dolor dolorem molestiae nobis, adipisci voluptates magnam aliquid
                nulla rerum ut tenetur minus quidem natus similique blanditiis ducimus. Ex iusto distinctio quidem
                voluptatum, praesentium inventore.
            </div>
        </div>
    </AppLayout>





    <!-- #endregion -->
</template>

<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    /* object-position: 50% 30%; */
    border-radius: 5px 10px;
    object-fit: cover;
    /* Opciones: 'cover', 'contain', 'fill', etc. */
}

.info-resto {
    font-size: 15px;
    text-wrap: balance;
    opacity: .5;
}
</style>