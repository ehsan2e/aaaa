<template>
    <div>
        <i class="fa fa-circle" :class="{'text-danger': !ready, 'text-success': ready}"></i>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>IP</th>
                <th>CPU%</th>
                <th>Memory%</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="server in serversArray" :key="server.ip">
                <td>{{ server.ip }}</td>
                <td>{{ server.cpu.toFixed(2) }}</td>
                <td>{{ server.memory.toFixed(2) }}</td>
            </tr>
            <tr key="empty-row" class="text-center" v-if="serversArray.length === 0">
                <td colspan="3">No Item Found</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "box-monitor-component",
        data() {
            return {
                ready: false,
                retry: true,
                ws: null,
                serversTracker: 1,
                servers: new Map(),
            }
        },
        computed: {
            serversArray() {
                return this.serversTracker && Array.from(this.servers).map((item) => item[1]);
            }
        },
        methods: {
            connect() {
                this.ready = false;
                try {
                    if (this.ws) {
                        this.ws.close();
                    }
                } catch (error) {
                    console.error(error);
                }
                this.ws = null;
                this.ws = new WebSocket(this.url);
                this.ws.onopen = () => {
                    this.ready = true;
                };
                this.ws.onmessage = (event) => {
                    try {
                        const payload = JSON.parse(event.data || '{}');
                        switch (payload.type || '') {
                            case 'event':
                                switch (payload.name || '') {
                                    case 'state':
                                        if (this.servers.size === 0) {
                                            for (let i = 0; i < (payload.data || []).length; i++) {
                                                this.servers.set(payload.data[i].ip, payload.data[i]);
                                            }
                                            this.serversTracker++;
                                        }
                                        break;
                                    case 'box-state':
                                        this.servers.set(payload.data.ip, payload.data);
                                        this.serversTracker++;
                                        break;
                                }
                                break;
                        }
                    } catch (error) {
                        console.error(error);
                    }
                };
                this.ws.onclose = () => {
                    this.ws = null;
                    this.ready = false;
                    this.servers = new Map();
                    this.serversTracker++;
                };
                this.ws.onerror = console.error;
            }
        },
        created() {
            this.connect();
        },

        props: {
            url: {
                type: String,
                required: true
            }
        }
    }
</script>