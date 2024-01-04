<script setup>
import { parseFieldSpecs } from '@fullcalendar/core/internal';
import axios from 'axios';
import Dropdown from 'primevue/dropdown';
import { ref } from 'vue';

//#region paises
// const paises = [
//     { "code": "ad", "name": "ANDORRA" }, { "code": "ae", "name": "EMIRATOS ÁRABES UNIDOS" }, { "code": "af", "name": "AFGANISTÁN" }, { "code": "ag", "name": "ANTIGUA Y BARBUDA" }, { "code": "ai", "name": "ANGUILA" }, { "code": "al", "name": "ALBANIA" }, { "code": "am", "name": "ARMENIA" }, { "code": "ao", "name": "ANGOLA" }, { "code": "aq", "name": "ANTÁRTIDA" }, { "code": "ar", "name": "ARGENTINA" }, { "code": "as", "name": "SAMOA AMERICANA" }, { "code": "at", "name": "AUSTRIA" }, { "code": "au", "name": "AUSTRALIA" }, { "code": "aw", "name": "ARUBA" }, { "code": "ax", "name": "ÅLAND" }, { "code": "az", "name": "AZERBAIYÁN" }, { "code": "ba", "name": "BOSNIA Y HERZEGOVINA" }, { "code": "bb", "name": "BARBADOS" }, { "code": "bd", "name": "BANGLADESH" }, { "code": "be", "name": "BÉLGICA" }, { "code": "bf", "name": "BURKINA FASO" }, { "code": "bg", "name": "BULGARIA" }, { "code": "bh", "name": "BARÉIN" }, { "code": "bi", "name": "BURUNDI" }, { "code": "bj", "name": "BENÍN" }, { "code": "bl", "name": "SAN BARTOLOMÉ" }, { "code": "bm", "name": "BERMUDAS" }, { "code": "bn", "name": "BRUNÉI" }, { "code": "bo", "name": "BOLIVIA" }, { "code": "bq", "name": "CARIBE NEERLANDÉS" }, { "code": "br", "name": "BRASIL" }, { "code": "bs", "name": "BAHAMAS" }, { "code": "bt", "name": "BUTÁN" }, { "code": "bv", "name": "ISLA BOUVET" }, { "code": "bw", "name": "BOTSUANA" }, { "code": "by", "name": "BIELORRUSIA" }, { "code": "bz", "name": "BELICE" }, { "code": "ca", "name": "CANADÁ" }, { "code": "cc", "name": "ISLAS COCOS" }, { "code": "cd", "name": "CONGO (REP. DEM.)" }, { "code": "cf", "name": "REPÚBLICA CENTROAFRICANA" }, { "code": "cg", "name": "CONGO" }, { "code": "ch", "name": "SUIZA" }, { "code": "ci", "name": "COSTA DE MARFIL" }, { "code": "ck", "name": "ISLAS COOK" }, { "code": "cl", "name": "CHILE" }, { "code": "cm", "name": "CAMERÚN" }, { "code": "cn", "name": "CHINA" }, { "code": "co", "name": "COLOMBIA" }, { "code": "cr", "name": "COSTA RICA" }, { "code": "cu", "name": "CUBA" }, { "code": "cv", "name": "CABO VERDE" }, { "code": "cw", "name": "CURAZAO" }, { "code": "cx", "name": "ISLA DE NAVIDAD" }, { "code": "cy", "name": "CHIPRE" }, { "code": "cz", "name": "REPÚBLICA CHECA" }, { "code": "de", "name": "ALEMANIA" }, { "code": "dj", "name": "YIBUTI" }, { "code": "dk", "name": "DINAMARCA" }, { "code": "dm", "name": "DOMINICA" }, { "code": "do", "name": "REPÚBLICA DOMINICANA" }, { "code": "dz", "name": "ARGELIA" }, { "code": "ec", "name": "ECUADOR" }, { "code": "ee", "name": "ESTONIA" }, { "code": "eg", "name": "EGIPTO" }, { "code": "eh", "name": "SAHARA OCCIDENTAL" }, { "code": "er", "name": "ERITREA" }, { "code": "es", "name": "ESPAÑA" }, { "code": "et", "name": "ETIOPÍA" }, { "code": "eu", "name": "UNIÓN EUROPEA" }, { "code": "fi", "name": "FINLANDIA" }, { "code": "fj", "name": "FIYI" }, { "code": "fk", "name": "ISLAS MALVINAS" }, { "code": "fm", "name": "MICRONESIA" }, { "code": "fo", "name": "ISLAS FEROE" }, { "code": "fr", "name": "FRANCIA" }, { "code": "ga", "name": "GABÓN" }, { "code": "gb", "name": "REINO UNIDO" }, { "code": "gb-eng", "name": "INGLATERRA" }, { "code": "gb-nir", "name": "IRLANDA DEL NORTE" }, { "code": "gb-sct", "name": "ESCOCIA" }, { "code": "gb-wls", "name": "GALES" }, { "code": "gd", "name": "GRANADA" }, { "code": "ge", "name": "GEORGIA" }, { "code": "gf", "name": "GUAYANA FRANCESA" }, { "code": "gg", "name": "GUERNSEY" }, { "code": "gh", "name": "GHANA" }, { "code": "gi", "name": "GIBRALTAR" }, { "code": "gl", "name": "GROENLANDIA" }, { "code": "gm", "name": "GAMBIA" }, { "code": "gn", "name": "GUINEA" }, { "code": "gp", "name": "GUADALUPE" }, { "code": "gq", "name": "GUINEA ECUATORIAL" }, { "code": "gr", "name": "GRECIA" }, { "code": "gs", "name": "ISLAS GEORGIAS DEL SUR Y SÁNDWICH DEL SUR" }, { "code": "gt", "name": "GUATEMALA" }, { "code": "gu", "name": "GUAM" }, { "code": "gw", "name": "GUINEA-BISÁU" }, { "code": "gy", "name": "GUYANA" }, { "code": "hk", "name": "HONG KONG" }, { "code": "hm", "name": "ISLAS HEARD Y MCDONALD" }, { "code": "hn", "name": "HONDURAS" }, { "code": "hr", "name": "CROACIA" }, { "code": "ht", "name": "HAITÍ" }, { "code": "hu", "name": "HUNGRÍA" }, { "code": "id", "name": "INDONESIA" }, { "code": "ie", "name": "IRLANDA" }, { "code": "il", "name": "ISRAEL" }, { "code": "im", "name": "ISLA DE MAN" }, { "code": "in", "name": "INDIA" }, { "code": "io", "name": "TERRITORIO BRITÁNICO DEL OCÉANO ÍNDICO" }, { "code": "iq", "name": "IRAK" }, { "code": "ir", "name": "IRÁN" }, { "code": "is", "name": "ISLANDIA" }, { "code": "it", "name": "ITALIA" }, { "code": "je", "name": "JERSEY" }, { "code": "jm", "name": "JAMAICA" }, { "code": "jo", "name": "JORDANIA" }, { "code": "jp", "name": "JAPÓN" }, { "code": "ke", "name": "KENIA" }, { "code": "kg", "name": "KIRGUISTÁN" }, { "code": "kh", "name": "CAMBOYA" }, { "code": "ki", "name": "KIRIBATI" }, { "code": "km", "name": "COMORAS" }, { "code": "kn", "name": "SAN CRISTÓBAL Y NIEVES" }, { "code": "kp", "name": "COREA DEL NORTE" }, { "code": "kr", "name": "COREA DEL SUR" }, { "code": "kw", "name": "KUWAIT" }, { "code": "ky", "name": "ISLAS CAIMÁN" }, { "code": "kz", "name": "KAZAJISTÁN" }, { "code": "la", "name": "LAOS" }, { "code": "lb", "name": "LÍBANO" }, { "code": "lc", "name": "SANTA LUCÍA" }, { "code": "li", "name": "LIECHTENSTEIN" }, { "code": "lk", "name": "SRI LANKA" }, { "code": "lr", "name": "LIBERIA" }, { "code": "ls", "name": "LESOTO" }, { "code": "lt", "name": "LITUANIA" }, { "code": "lu", "name": "LUXEMBURGO" }, { "code": "lv", "name": "LETONIA" }, { "code": "ly", "name": "LIBIA" }, { "code": "ma", "name": "MARRUECOS" }, { "code": "mc", "name": "MÓNACO" }, { "code": "md", "name": "MOLDAVIA" }, { "code": "me", "name": "MONTENEGRO" }, { "code": "mf", "name": "SAN MARTÍN (FRANCIA)" }, { "code": "mg", "name": "MADAGASCAR" }, { "code": "mh", "name": "ISLAS MARSHALL" }, { "code": "mk", "name": "MACEDONIA DEL NORTE" }, { "code": "ml", "name": "MALÍ" }, { "code": "mm", "name": "MYANMAR" }, { "code": "mn", "name": "MONGOLIA" }, { "code": "mo", "name": "MACAO" }, { "code": "mp", "name": "ISLAS MARIANAS DEL NORTE" }, { "code": "mq", "name": "MARTINICA" }, { "code": "mr", "name": "MAURITANIA" }, { "code": "ms", "name": "MONTSERRAT" }, { "code": "mt", "name": "MALTA" }, { "code": "mu", "name": "MAURICIO" }, { "code": "mv", "name": "MALDIVAS" }, { "code": "mw", "name": "MALAWI" }, { "code": "mx", "name": "MÉXICO" }, { "code": "my", "name": "MALASIA" }, { "code": "mz", "name": "MOZAMBIQUE" }, { "code": "na", "name": "NAMIBIA" }, { "code": "nc", "name": "NUEVA CALEDONIA" }, { "code": "ne", "name": "NÍGER" }, { "code": "nf", "name": "ISLA NORFOLK" }, { "code": "ng", "name": "NIGERIA" }, { "code": "ni", "name": "NICARAGUA" }, { "code": "nl", "name": "PAÍSES BAJOS" }, { "code": "no", "name": "NORUEGA" }, { "code": "np", "name": "NEPAL" }, { "code": "nr", "name": "NAURU" }, { "code": "nu", "name": "NIUE" }, { "code": "nz", "name": "NUEVA ZELANDA" }, { "code": "om", "name": "OMÁN" }, { "code": "pa", "name": "PANAMÁ" }, { "code": "pe", "name": "PERÚ" }, { "code": "pf", "name": "POLINESIA FRANCESA" }, { "code": "pg", "name": "PAPÚA NUEVA GUINEA" }, { "code": "ph", "name": "FILIPINAS" }, { "code": "pk", "name": "PAKISTÁN" }, { "code": "pl", "name": "POLONIA" }, { "code": "pm", "name": "SAN PEDRO Y MIQUELÓN" }, { "code": "pn", "name": "ISLAS PITCAIRN" }, { "code": "pr", "name": "PUERTO RICO" }, { "code": "ps", "name": "PALESTINA" }, { "code": "pt", "name": "PORTUGAL" }, { "code": "pw", "name": "PALAOS" }, { "code": "py", "name": "PARAGUAY" }, { "code": "qa", "name": "CATAR" }, { "code": "re", "name": "REUNIÓN" }, { "code": "ro", "name": "RUMANIA" }, { "code": "rs", "name": "SERBIA" }, { "code": "ru", "name": "RUSIA" }, { "code": "rw", "name": "RUANDA" }, { "code": "sa", "name": "ARABIA SAUDITA" }, { "code": "sb", "name": "ISLAS SALOMÓN" }, { "code": "sc", "name": "SEYCHELLES" }, { "code": "sd", "name": "SUDÁN" }, { "code": "se", "name": "SUECIA" }, { "code": "sg", "name": "SINGAPUR" }, { "code": "sh", "name": "SANTA ELENA, ASCENSIÓN Y TRISTÁN DE ACUÑA" }, { "code": "si", "name": "ESLOVENIA" }, { "code": "sj", "name": "SVALBARD Y JAN MAYEN" }, { "code": "sk", "name": "ESLOVAQUIA" }, { "code": "sl", "name": "SIERRA LEONA" }, { "code": "sm", "name": "SAN MARINO" }, { "code": "sn", "name": "SENEGAL" }, { "code": "so", "name": "SOMALIA" }, { "code": "sr", "name": "SURINAM" }, { "code": "ss", "name": "SUDÁN DEL SUR" }, { "code": "st", "name": "SANTO TOMÉ Y PRÍNCIPE" }, { "code": "sv", "name": "EL SALVADOR" }, { "code": "sx", "name": "SAN MARTÍN (PAÍSES BAJOS)" }, { "code": "sy", "name": "SIRIA" }, { "code": "sz", "name": "SUAZILANDIA" }, { "code": "tc", "name": "ISLAS TURCAS Y CAICOS" }, { "code": "td", "name": "CHAD" }, { "code": "tf", "name": "TIERRAS AUSTRALES Y ANTÁRTICAS FRANCESAS" }, { "code": "tg", "name": "TOGO" }, { "code": "th", "name": "TAILANDIA" }, { "code": "tj", "name": "TAYIKISTÁN" }, { "code": "tk", "name": "TOKELAU" }, { "code": "tl", "name": "TIMOR ORIENTAL" }, { "code": "tm", "name": "TURKMENISTÁN" }, { "code": "tn", "name": "TÚNEZ" }, { "code": "to", "name": "TONGA" }, { "code": "tr", "name": "TURQUÍA" }, { "code": "tt", "name": "TRINIDAD Y TOBAGO" }, { "code": "tv", "name": "TUVALU" }, { "code": "tw", "name": "TAIWÁN" }, { "code": "tz", "name": "TANZANIA" }, { "code": "ua", "name": "UCRANIA" }, { "code": "ug", "name": "UGANDA" }, { "code": "um", "name": "ISLAS ULTRAMARINAS MENORES DE LOS ESTADOS UNIDOS" }, { "code": "un", "name": "ORGANIZACIÓN DE LAS NACIONES UNIDAS" }, { "code": "us", "name": "ESTADOS UNIDOS" }, { "code": "uy", "name": "URUGUAY" }, { "code": "uz", "name": "UZBEKISTÁN" }, { "code": "va", "name": "CIUDAD DEL VATICANO" }, { "code": "vc", "name": "SAN VICENTE Y LAS GRANADINAS" }, { "code": "ve", "name": "VENEZUELA" }, { "code": "vg", "name": "ISLAS VÍRGENES BRITÁNICAS" }, { "code": "vi", "name": "ISLAS VÍRGENES DE LOS ESTADOS UNIDOS" }, { "code": "vn", "name": "VIETNAM" }, { "code": "vu", "name": "VANUATU" }, { "code": "wf", "name": "WALLIS Y FUTUNA" }, { "code": "ws", "name": "SAMOA" }, { "code": "xk", "name": "KOSOVO" }, { "code": "ye", "name": "YEMEN" }, { "code": "yt", "name": "MAYOTTE" }, { "code": "za", "name": "SUDÁFRICA" }, { "code": "zm", "name": "ZAMBIA" }, { "code": "zw", "name": "ZIMBABUE" }]
//#endregion


const paises = ref()
axios.get('https://restcountries.com/v3.1/all?fields=flags,translations').then(
    (res) => {
        paises.value = res.data.map(pais => {
            pais.translations.spa.common = pais.translations.spa.common.toUpperCase()
            return pais
        })
    }
)

const selected = defineModel('selected', {
    required: true
})

</script>
<template>
    <Dropdown v-if="paises" v-model="selected" :options="paises" filter resetFilterOnHide
        optionValue="translations.spa.common" optionLabel="translations.spa.common" placeholder="Selecciona el pais"
        class="w-full -mt-1 rounded-md md:w-14rem" :pt="{
            root: {
                class: 'h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
            },
            input: {
                class: '!text-sm pt-3 pl-2'
            },
            item: {
                class: '!text-sm'
            }
        }">
        <template #value="slotProps">
            <div v-if="slotProps.value" class="flex space-x-1">
                <img :src="paises.find(objeto =>
                    objeto.translations.spa.common === slotProps.value.toUpperCase()).flags.svg" width="30"
                    :alt="slotProps.value">
                <p class="">{{ slotProps.value }}</p>
            </div>
            <span v-else>
                {{ slotProps.placeholder }}
            </span>
        </template>
        <template #option="slotProps">
            <div class="flex space-x-1">
                <img :src="slotProps.option.flags.svg" width="30" :alt="slotProps.option.translations.spa.common">
                <p>{{ slotProps.option.translations.spa.common }}</p>
            </div>
        </template>

    </Dropdown>
</template>

