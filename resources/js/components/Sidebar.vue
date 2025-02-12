<script setup>
import {ref} from 'vue'
import {useMenuStore} from '../stores/menuStore'

const props = defineProps({
    routes: {
        type: Object,
        required: true,
    },
    uris: {
        type: Object,
        required: true,
    },
    current: {
        type: String,
        required: true,
    },
});

const items = ref([
    {
        text: 'Locations',
        icon: 'pi pi-map-marker',
        expanded: false,
        links: [
            {
                type: 'link',
                text: 'All Locations',
                route: 'admin.application.index',
            },
        ],
    },
    {
        text: 'Settings',
        icon: 'pi pi-cog',
        expanded: false,
        links: [
            {
                type: 'heading',
                text: 'Settings',
            },
            {
                type: 'link',
                text: 'Company Settings',
                route: 'admin.company.edit',
            },
            {
                type: 'link',
                text: 'Application Settings',
                route: 'admin.application.index',
            },
        ],
    },
    {
        text: 'Calendars',
        icon: 'pi pi-calendar',
        expanded: false,
        links: [
            {
                type: 'link',
                text: 'Create Calendar',
                route: 'admin.calendars.create',
            },
            {
                type: 'link',
                text: 'All Calendars',
                route: 'admin.calendars.index',
            },
        ],
    },
    {
        text: 'Users',
        icon: 'pi pi-users',
        expanded: false,
        links: [
            {
                type: 'heading',
                text: 'Users',
            },
            {
                type: 'link',
                text: 'All Users',
                route: 'admin.users.index'
            },
            {
                type: 'link',
                text: 'Create User',
                route: 'admin.users.create'
            },
        ],
    },
    {
        text: 'Sales Orders',
        icon: 'pi pi-dollar',
        expanded: false,
        links: [
            {
                type: 'link',
                text: 'All Sales Orders',
                route: 'admin.sales-orders.index'
            },
            {
                type: 'link',
                text: 'Create Sales Order',
                route: 'admin.sales-orders.create'
            },
        ],
    },
])

items.value.forEach(item => {
    item.expanded = item.links.some((subItem) => {
        return subItem.type !== 'heading' && subItem.route === props.routes[props.current]
    })
});

const toggleExpanded = (item) => {
    item.expanded = !item.expanded
}

const store = useMenuStore()

const closeMenu = () => {
    store.isMenuVisible = false
    document.documentElement.classList.remove('overflow-hidden');
    document.body.classList.remove('overflow-hidden')
}

const checkScreenSize = () => {
    store.isLargeScreen = window.innerWidth >= 1024

    if (store.isLargeScreen) {
        closeMenu()
    }
}

checkScreenSize()
window.addEventListener('resize', checkScreenSize)
</script>

<template>
    <div v-if="store.isMenuVisible && !store.isLargeScreen"
         class="opacity-50 bg-gray-900 z-150 w-full h-full left-0 top-0 fixed"
         @click="closeMenu"
    ></div>

    <nav v-if="store.isMenuVisible && !store.isLargeScreen || store.isLargeScreen"
         class="bg-white border-r w-[280px] border-gray-200 overflow-y-auto"
         :class="{'z-200 fixed h-full top-0 left-0': !store.isLargeScreen && store.isMenuVisible}"
    >
        <div v-if="store.isMenuVisible && !store.isLargeScreen"
             class="flex pt-2 pr-4"
        >

            <button class="ml-auto flex items-center text-sm text-slate-700" @click="closeMenu">Close</button>
        </div>

        <ul class="p-4">
            <li v-for="item in items" class="mb-4">
                <button @click="toggleExpanded(item)" type="button" class="flex items-center w-full text-slate-700">
                    <span
                        class="rounded-sm h-8 w-8 mr-3 justify-center inline-flex items-center border border-gray-200">
                        <i :class="item.icon"></i>
                    </span>
                    <span class="text-sm font-bold">{{ item.text }}</span>
                    <span class="flex-1 text-right">
                    <i :class="{'pi pi-angle-down': !item.expanded, 'pi pi-angle-up': item.expanded}"></i>
                </span>
                </button>
                <ul v-show="item.expanded" class="ml-4 mt-2">
                    <li v-for="link in item.links">
                        <div v-if="link.type === 'heading'" class="text-sm font-bold text-orange-600 py-1">
                            {{ link.text }}
                        </div>
                        <a v-else
                           :href="uris[link.route]"
                           :class="['text-sm hover:text-slate-800 hover:border-l-gray-400 px-3 py-2 block border-l', {'text-sky-800 border-sky-800': link.route === props.current, ' text-gray-600 border-gray-200': link.route !== props.current}]"
                        >
                            {{ link.text }}
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>
