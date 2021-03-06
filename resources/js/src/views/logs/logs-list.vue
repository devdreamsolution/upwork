<template>
    <div>
        <h2 class="style-title">Devices</h2>
        <div class="logs-search w-full">
            <p>Date from:</p>
            <datepicker v-model="search_params.date_from" class="logs-search-items" style="z-index:1000"></datepicker>
            <p>To:</p>
            <datepicker v-model="search_params.date_to" class="logs-search-items" style="z-index:1000"></datepicker>
            <p>Device group:</p>
            <vs-select v-model="search_params.device_group_id" @change="findDevices" class="logs-search-items">
                <vs-select-item key="blank" value="" text="-none-"/>
                <vs-select-item v-for="data in device_groups" :key="data.id" :value="data.id" :text="data.name"/>
            </vs-select>
            <p v-if="devices.length > 0">Device:</p>
            <p v-if="devices.length < 1 && search_params.device_group_id ">No devices found</p>
            <vs-select v-if="devices.length > 0" v-model="search_params.device_id" class="logs-search-items">
                <vs-select-item key="blank" value="" text="-none-"/>
                <vs-select-item v-for="data in devices" :key="data.id" :value="data.id" :text="data.name"/>
            </vs-select>
        </div>
        <div class="logs-search-buttons w-full">
            <vs-button v-on:click="search" class="mr-3 mb-2 smaller-button">Search</vs-button>
            <vs-button v-on:click="clear" class="mr-3 mb-2 smaller-button" color="danger">Clear</vs-button>
            <vs-button v-on:click="exportData" class="mr-3 mb-2 smaller-button">Export</vs-button>
        </div>
        <vs-table data="users">
            <template slot="thead">
                <vs-th>ID</vs-th>
                <vs-th>Device group</vs-th>
                <vs-th>Device</vs-th>
                <vs-th>Description</vs-th>
                <vs-th>Reported by</vs-th>
                <vs-th>Reported at</vs-th>
            </template>

            <template>
                <vs-tr v-for="item in data" v-bind:key="item.id">
                    <vs-td>{{item.id}}</vs-td>
                    <vs-td><span v-if="item.device && item.device.device_group">{{item.device.device_group.name}}</span></vs-td>
                    <vs-td><span v-if="item.device">{{item.device.name}}</span></vs-td>
                    <vs-td>{{item.event_desc}}</vs-td>
                    <vs-td><span v-if="item.reported_by">{{item.reported_by.name}}</span></vs-td>
                    <vs-td>{{item.reported_at}}</vs-td>
                </vs-tr>
            </template>

        </vs-table>
    </div>
</template>
<script>

    import Datepicker from 'vuejs-datepicker';

    export default {
        data() {
            return {
                id: '',
                data: [],
                devices: [],
                device_groups: [],
                search_params: {
                    date_from: '',
                    date_to: '',
                    device_group_id: '',
                    device_id: ''
                }
            }
        },
        components: {
            Datepicker
        },
        computed: {
            token() {
                return JSON.parse(localStorage.user).api_token
            }
        },
        methods: {
            search() {
                this.data = []
                this.$axios.get('/api/admin/logs', {
                    params: {
                        api_token: this.token,
                        ...this.search_params
                    }
                }).then((res) => {
                    this.data = res.data.logs
                    this.device_groups = res.data.device_groups
                }).catch((err) => {
                    console.log(err);
                })
            },
            clear() {
                this.search_params = {
                    date_from: null,
                    date_to: null,
                    device_group_id: '',
                    device_id: ''
                }
                this.devices = []
                this.search()
            },
            exportData() {
                let user = JSON.parse(localStorage.user)
                let token = user.api_token
                this.$axios({
                    method: 'GET',
                    url: '/api/admin/export/logs/csv',
                    responseType: 'blob',
                    params: {
                        api_token: token,
                        date_from: this.search_params.date_from,
                        date_to: this.search_params.date_to,
                        device_group_id: this.search_params.device_group_id,
                        device_id: this.search_params.device_id,
                        logs: this.data
                    },
                }).then((res) => {
                    const url = window.URL.createObjectURL(new Blob([res.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'LogsReport.xls');
                    document.body.appendChild(link);
                    link.click();
                })
            },
            findDevices() {
                this.devices = []
                this.search_params.device_id = ''
                let user = JSON.parse(localStorage.user)
                let token = user.api_token
                if (this.search_params.device_group_id) {
                    this.$axios.get('/api/admin/devices/' + this.search_params.device_group_id, {params: {api_token: token}}).then((res) => {
                        this.devices = res.data;
                    }).catch((err) => {
                        console.log(err);
                    })
                }
            }
        },
        beforeMount() {
            this.search()
        },
    }
</script>
