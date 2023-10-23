<template>
    <div class="py-32 bg-white">
        <div class="px-6 mx-auto text-center transition max-w-7xl lg:px-8">
            <div class="max-w-3xl mx-auto">
                <h2
                    class="text-3xl font-black tracking-tight text-transparent text-gray-900 bg-gradient-to-r bg-clip-text from-indigo-500 via-purple-500 to-indigo-500 animate-text sm:text-4xl">
                    Conoce Nuestro Equipo de Expertos
                </h2>
                <p class="mt-4 text-lg leading-8 text-gray-600 ourTeamDescription">
                    En <strong>Cotecmar</strong>, creemos que el éxito de un proyecto
                    radica en las personas que lo hacen posible. Conoce a nuestro talentoso equipo de desarrolladores
                    web, diseñadores y expertos digitales que trabajan juntos para dar vida a tus ideas en línea.
                </p>
            </div>
            <ul role="list"
                class="grid max-w-2xl grid-cols-1 mx-auto mt-20 gap-x-8 gap-y-16 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                <li v-for="person in team" :key="person.name">
                    <img class="w-56 h-56 mx-auto rounded-full shadow-lg" :src="person.imageUrl"
                        alt="Foto Perfil de Miembro del Equipo"
                        onerror="this.onerror=null; this.src='https://images.assetsdelivery.com/compings_v2/thesomeday123/thesomeday1231709/thesomeday123170900021.jpg'" />
                    <h3 class="mt-6 text-base font-semibold leading-7 tracking-tight text-gray-900 ">{{ person.name }}
                    </h3>
                    <p class="text-sm leading-6 text-gray-600 ">{{ person.role }}</p>
                    <p class="text-sm leading-6 text-gray-600">{{ person.email }}</p>
                    <ul role="list" class="flex justify-center mt-6 align-content-center gap-x-6">
                        <li>
                            <a :href="person.gitHub" class="text-gray-400 hover:text-gray-500" target="_blank">
                                <span class="sr-only">GitHub</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" aria-hidden="true"
                                    fill="currentColor" viewBox="0 0 30 30">
                                    <path
                                        d="M15,3C8.373,3,3,8.373,3,15c0,5.623,3.872,10.328,9.092,11.63C12.036,26.468,12,26.28,12,26.047v-2.051 c-0.487,0-1.303,0-1.508,0c-0.821,0-1.551-0.353-1.905-1.009c-0.393-0.729-0.461-1.844-1.435-2.526 c-0.289-0.227-0.069-0.486,0.264-0.451c0.615,0.174,1.125,0.596,1.605,1.222c0.478,0.627,0.703,0.769,1.596,0.769 c0.433,0,1.081-0.025,1.691-0.121c0.328-0.833,0.895-1.6,1.588-1.962c-3.996-0.411-5.903-2.399-5.903-5.098 c0-1.162,0.495-2.286,1.336-3.233C9.053,10.647,8.706,8.73,9.435,8c1.798,0,2.885,1.166,3.146,1.481C13.477,9.174,14.461,9,15.495,9 c1.036,0,2.024,0.174,2.922,0.483C18.675,9.17,19.763,8,21.565,8c0.732,0.731,0.381,2.656,0.102,3.594 c0.836,0.945,1.328,2.066,1.328,3.226c0,2.697-1.904,4.684-5.894,5.097C18.199,20.49,19,22.1,19,23.313v2.734 c0,0.104-0.023,0.179-0.035,0.268C23.641,24.676,27,20.236,27,15C27,8.373,21.627,3,15,3z" />
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a :href="person.twitterUrl" class="text-gray-400 hover:text-gray-500" target="_blank">
                                <span class="sr-only">Twitter</span>
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a :href="person.linkedinUrl" class="text-gray-400 hover:text-gray-500" target="_blank">
                                <span class="sr-only">LinkedIn</span>
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
//#region Team Members
const team = [
    {
        name: 'Deivi Ibarra Negrette',
        role: 'Líder de Aplicaciones y Bases de Datos',
        email: 'dibarra@cotecmar.com',
        imageUrl:
            '',
        gitHub: '#',
        twitterUrl: '#',
        linkedinUrl: '#',
    },
    {
        name: 'Erick Cantillo Jiménez',
        role: 'Analista de Desarrollo',
        email: 'ecantillo@cotecmar.com',
        imageUrl:
            '',
        gitHub: '#',
        twitterUrl: '#',
        linkedinUrl: '#',
    },
    {
        name: 'Ronny Gutierrez Vitola',
        role: 'Técnico Análisis y Diseño de Aplicaciones',
        email: 'rgutierrez@cotecmar.com',
        imageUrl:
            'https://media.licdn.com/dms/image/C5603AQG05bpoNmxzxQ/profile-displayphoto-shrink_800_800/0/1517237750772?e=2147483647&v=beta&t=kKO_OVsMWiuP_piAb9gp5zDtIUrKgTLXuf09-fiPXlQ',
        gitHub: 'https://github.com/RonCode144',
        twitterUrl: 'https://twitter.com/roniiskyz',
        linkedinUrl: 'https://co.linkedin.com/in/ronny-gutierrez-vitola',
    },
    {
        name: 'Giovani Buelvas Jaspe',
        role: 'Técnico Análisis y Diseño de Aplicaciones',
        email: 'gbuelvas@cotecmar.com',
        imageUrl:
            '',
        gitHub: '#',
        twitterUrl: '#',
        linkedinUrl: '#',
    },
    // More people...
]
//#endregion
</script>

<style scoped lang="css">
.ourTeamDescription {
    text-wrap: balance;
}

/* Transitions */
.transition {

    animation: fadeInAnimation ease 2s;
    animation-iteration-count: 1;
    animation-fill-mode: both;
}

@keyframes fadeInAnimation {
    0% {
        opacity: 0;
        scale: calc(0);
    }

    100% {
        opacity: 1;
        scale: calc(1)
    }
}
</style>
