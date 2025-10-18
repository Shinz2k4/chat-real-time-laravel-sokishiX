<template>
    <div class="friends-panel">
        <div class="row g-3">
            <!-- Left column: Friends list -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-custom h-100">
                    <div class="card-header bg-light fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-user-check"></i>
                        Bạn bè
                        <span class="badge rounded-pill bg-secondary ms-auto">{{ friends.length }}</span>
                    </div>
                    <div class="card-body p-2">
                        <div v-if="friends.length" class="list-scroll">
                            <div v-for="f in friends" :key="f._id" class="request-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <img :src="avatar(f)" class="avatar" alt="avatar">
                                    <div class="d-flex flex-column">
                                        <strong>@{{ f.username }}</strong>
                                        <small class="text-muted">{{ f.name }}</small>
                                    </div>
                                </div>
                                <button class="btn btn-outline-danger btn-sm" :disabled="busy" @click="unfriend(f._id)"><i class="fas fa-user-minus me-1"></i> Hủy</button>
                            </div>
                        </div>
                        <div v-else class="text-muted small px-2 py-1">
                            Chưa có bạn bè ({{ friends.length }})
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right column: Add friend + Requests -->
            <div class="col-12 col-lg-6 d-flex flex-column gap-3">
                <!-- Add friend -->
                <div class="card shadow-custom">
                    <div class="card-header d-flex align-items-center justify-content-between bg-gradient-dark text-white">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            <span class="fw-bold">Kết bạn</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                            <input v-model.trim="query" @keyup.enter="search" type="text" class="form-control" placeholder="Tìm theo username...">
                            <button class="btn btn-primary" :disabled="!query || loading" @click="search">
                                <i class="fas fa-search me-1" :class="{ 'fa-spin': loading }"></i>
                                Tìm
                            </button>
                        </div>
                        <div v-if="results.length" class="search-results list-scroll">
                            <div v-for="u in results" :key="u._id" class="result-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <img :src="avatar(u)" class="avatar" alt="avatar">
                                    <div class="d-flex flex-column">
                                        <strong>@{{ u.username }}</strong>
                                        <small class="text-muted">{{ u.name }}</small>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary btn-sm" :disabled="busy" @click="send(u._id)">
                                    <i class="fas fa-user-plus me-1"></i> Mời kết bạn
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-muted small">Nhập username và nhấn Tìm</div>
                    </div>
                </div>

                <!-- Incoming requests -->
                <div class="card shadow-custom">
                    <div class="card-header bg-light fw-bold d-flex align-items-center">
                        <span>Lời mời đến</span>
                        <span class="badge rounded-pill bg-danger ms-auto">{{ incoming.length }}</span>
                    </div>
                    <div class="card-body p-2">
                        <div v-if="incoming.length" class="list-scroll">
                            <div v-for="it in incoming" :key="it._id" class="request-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <img :src="avatar(it.from_user)" class="avatar" alt="avatar">
                                    <div class="d-flex flex-column">
                                        <strong>@{{ it.from_user?.username }}</strong>
                                        <small class="text-muted">{{ it.from_user?.name }}</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success btn-sm" :disabled="busy" @click="respond(it._id, 'accept')"><i class="fas fa-check"></i></button>
                                    <button class="btn btn-outline-secondary btn-sm" :disabled="busy" @click="respond(it._id, 'decline')"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-muted small px-2 py-1">Không có lời mời ({{ incoming.length }})</div>
                    </div>
                </div>

                <!-- Outgoing requests -->
                <div class="card shadow-custom">
                    <div class="card-header bg-light fw-bold d-flex align-items-center">
                        <span>Đã gửi</span>
                        <span class="badge rounded-pill bg-warning text-dark ms-auto">{{ outgoing.length }}</span>
                    </div>
                    <div class="card-body p-2">
                        <div v-if="outgoing.length" class="list-scroll">
                            <div v-for="it in outgoing" :key="it._id" class="request-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <img :src="avatar(it.to_user)" class="avatar" alt="avatar">
                                    <div class="d-flex flex-column">
                                        <strong>@{{ it.to_user?.username }}</strong>
                                        <small class="text-muted">{{ it.to_user?.name }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-warning">Đang chờ</span>
                            </div>
                        </div>
                        <div v-else class="text-muted small px-2 py-1">Chưa gửi lời mời nào ({{ outgoing.length }})</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script>
export default {
    name: 'FriendsPanel',
    emits: ['friends-changed','incoming-count'],
    expose: ['refreshData'],
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            query: '',
            results: [],
            friends: [],
            incoming: [],
            outgoing: [],
            loading: false,
            busy: false,
            cacheLoaded: false,
            dataLoaded: false, // Track if data has been loaded at least once
        }
    },
    methods: {
        // Cache methods
        getCacheKey(type) {
            const userId = this.user?._id || this.user?.id || 'guest';
            return `friends_${type}_${userId}`;
        },
        
        setCache(type, data) {
            try {
                const key = this.getCacheKey(type);
                const cacheData = {
                    data: data,
                    timestamp: Date.now()
                };
                localStorage.setItem(key, JSON.stringify(cacheData));
            } catch (e) {
                console.warn('Failed to cache friends data:', e);
            }
        },
        
        getCache(type, maxAge = 5 * 60 * 1000) { // 5 minutes default
            try {
                const key = this.getCacheKey(type);
                console.log(`🔍 Checking cache for key: ${key}`);
                const cached = localStorage.getItem(key);
                if (!cached) {
                    console.log(`❌ No cache found for ${type}`);
                    return null;
                }
                
                const cacheData = JSON.parse(cached);
                const age = Date.now() - cacheData.timestamp;
                const ageMinutes = Math.round(age / 60000);
                
                console.log(`📊 Cache age for ${type}: ${ageMinutes} minutes (max: ${Math.round(maxAge / 60000)} minutes)`);
                
                if (age > maxAge) {
                    console.log(`⏰ Cache expired for ${type}, removing...`);
                    localStorage.removeItem(key);
                    return null;
                }
                
                console.log(`✅ Cache valid for ${type}, returning data`);
                return cacheData.data;
            } catch (e) {
                console.warn('Failed to get cached friends data:', e);
                return null;
            }
        },
        
        clearCache() {
            try {
                const types = ['friends', 'incoming', 'outgoing'];
                types.forEach(type => {
                    const key = this.getCacheKey(type);
                    localStorage.removeItem(key);
                });
            } catch (e) {
                console.warn('Failed to clear friends cache:', e);
            }
        },
        
        avatar(user){
            if (!user || !user.profile_image || user.profile_image === 'default_image.png') {
                const ch = user && user.name ? user.name.charAt(0).toUpperCase() : 'U';
                return 'https://via.placeholder.com/40/6366f1/ffffff?text=' + ch;
            }
            if (user.profile_image.includes('res.cloudinary.com')) {
                return user.profile_image.replace('/upload/', '/upload/w_40,h_40,c_fill,g_face,q_auto,f_auto/');
            }
            return 'storage/profile_images/' + user.profile_image;
        },
        async search(){
            if (!this.query) return;
            this.loading = true;
            try{
                const { data } = await axios.get('/friends/search', { params: { q: this.query }});
                this.results = data;
            }catch(e){
                console.error(e);
            }finally{
                this.loading = false;
            }
        },
        async loadRequests(forceRefresh = false){
            try{
                // Try to get from cache first (unless force refresh)
                if (!forceRefresh) {
                    const cachedIncoming = this.getCache('incoming');
                    const cachedOutgoing = this.getCache('outgoing');
                    
                    if (cachedIncoming !== null && cachedOutgoing !== null) {
                        console.log('✅ Using cached requests data');
                        this.incoming = cachedIncoming;
                        this.outgoing = cachedOutgoing;
                        this.$emit('incoming-count', this.incoming.length);
                        return;
                    }
                }
                
                console.log('🔄 Loading requests from API...');
                console.log('Making requests to /friends/incoming and /friends/outgoing with user:', this.user);
                const [inc, out] = await Promise.all([
                    axios.get('/friends/incoming'),
                    axios.get('/friends/outgoing')
                ]);
                
                console.log('Raw API responses:', { incoming: inc.data, outgoing: out.data });
                
                // normalize relations for simpler template
                this.incoming = (inc.data || []).map(x => ({...x, from_user: x.from_user || x.fromUser}));
                this.outgoing = (out.data || []).map(x => ({...x, to_user: x.to_user || x.toUser}));
                
                // Cache the results
                this.setCache('incoming', this.incoming);
                this.setCache('outgoing', this.outgoing);
                
                this.$emit('incoming-count', this.incoming.length);
                console.log('✅ Requests loaded successfully:', { incoming: this.incoming.length, outgoing: this.outgoing.length });
            }catch(e){ 
                console.error('❌ Error loading requests:', e);
                this.incoming = [];
                this.outgoing = [];
            }
        },
        async loadFriends(forceRefresh = false){
            try{
                // Try to get from cache first (unless force refresh)
                if (!forceRefresh) {
                    const cached = this.getCache('friends');
                    if (cached !== null) {
                        console.log('✅ Using cached friends data');
                        this.friends = cached;
                        return;
                    }
                }
                
                console.log('🔄 Loading friends from API...');
                console.log('Making request to /friends with user:', this.user);
                const { data } = await axios.get('/friends');
                console.log('Raw friends API response:', data);
                this.friends = data || [];
                
                // Cache the result
                this.setCache('friends', this.friends);
                console.log('✅ Friends loaded successfully:', this.friends.length);
            }catch(e){ 
                console.error('❌ Error loading friends:', e);
                this.friends = [];
            }
        },
        async send(toId){
            this.busy = true;
            try{
                await axios.post('/friends/send', { to_user_id: toId });
                this.clearCache(); // Clear cache when data changes
                await this.loadRequests(true); // Force refresh
            }catch(e){ console.error(e); }
            finally{ this.busy = false; }
        },
        async respond(requestId, action){
            this.busy = true;
            try{
                await axios.post('/friends/respond', { request_id: requestId, action });
                this.clearCache(); // Clear cache when data changes
                await this.loadRequests(true); // Force refresh
                await this.loadFriends(true); // Force refresh
                if (action === 'accept') {
                    this.$emit('friends-changed');
                }
            }catch(e){ console.error(e); }
            finally{ this.busy = false; }
        }
        ,
        async unfriend(friendId){
            if (!confirm('Hủy kết bạn?')) return;
            this.busy = true;
            try{
                await axios.post('/friends/unfriend', { friend_user_id: friendId });
                this.clearCache(); // Clear cache when data changes
                await Promise.all([this.loadFriends(true), this.loadRequests(true)]); // Force refresh
                this.$emit('friends-changed');
            }catch(e){ console.error(e); }
            finally{ this.busy = false; }
        },
        
        async loadFromCache() {
            try {
                console.log('🔍 Checking cache for friends data...');
                const cachedFriends = this.getCache('friends');
                const cachedIncoming = this.getCache('incoming');
                const cachedOutgoing = this.getCache('outgoing');
                
                console.log('Cache status:', {
                    friends: cachedFriends ? 'found' : 'not found',
                    incoming: cachedIncoming ? 'found' : 'not found',
                    outgoing: cachedOutgoing ? 'found' : 'not found'
                });
                
                if (cachedFriends !== null) {
                    this.friends = cachedFriends;
                    console.log('✅ Loaded friends from cache:', this.friends.length);
                }
                if (cachedIncoming !== null) {
                    this.incoming = cachedIncoming;
                    this.$emit('incoming-count', this.incoming.length);
                    console.log('✅ Loaded incoming requests from cache:', this.incoming.length);
                }
                if (cachedOutgoing !== null) {
                    this.outgoing = cachedOutgoing;
                    console.log('✅ Loaded outgoing requests from cache:', this.outgoing.length);
                }
                
                this.cacheLoaded = true;
            } catch (e) {
                console.warn('Failed to load from cache:', e);
            }
        },
        
        // Method to refresh data when needed
        async refreshData() {
            this.clearCache();
            await Promise.all([
                this.loadRequests(true),
                this.loadFriends(true)
            ]);
        },
        
        // Check if cache is stale and needs refresh
        isCacheStale() {
            const maxAge = 5 * 60 * 1000; // 5 minutes (same as getCache default)
            const types = ['friends', 'incoming', 'outgoing'];
            
            for (const type of types) {
                const cached = this.getCache(type, maxAge);
                if (cached === null) {
                    console.log(`Cache is stale for ${type}`);
                    return true; // At least one cache is stale
                }
            }
            console.log('All caches are fresh');
            return false;
        },
        
        // Method to check if we need to refresh data when tab becomes visible
        async checkAndRefreshIfNeeded() {
            if (this.isCacheStale()) {
                console.log('Cache is stale, refreshing friends data...');
                await this.refreshData();
            }
        },
        
        // Method to refresh data from WebSocket events
        async refreshFromWebSocket() {
            console.log('🔄 Refreshing friends data from WebSocket event...');
            this.clearCache();
            await Promise.all([
                this.loadRequests(true),
                this.loadFriends(true)
            ]);
        }
    },
    watch: {
        // Watch for when component becomes visible (when tab switches)
        '$parent.currentTab': {
            handler(newTab) {
                if (newTab === 'friends' && this.dataLoaded) {
                    // Check if we need to refresh data when tab becomes visible
                    this.checkAndRefreshIfNeeded();
                }
            },
            immediate: false
        }
    },
    async mounted(){
        console.log('🚀 FriendsPanel mounted');
        console.log('User object:', this.user);
        console.log('Initial data state:', { 
            friends: this.friends.length, 
            incoming: this.incoming.length, 
            outgoing: this.outgoing.length 
        });
        
        // Check if user is authenticated
        if (!this.user || !this.user._id) {
            console.error('❌ User not authenticated or missing _id');
            return;
        }
        
        // Load from cache first for better UX
        await this.loadFromCache();
        
        console.log('After cache load:', { 
            friends: this.friends.length, 
            incoming: this.incoming.length, 
            outgoing: this.outgoing.length 
        });
        
        // Only load fresh data if not already loaded or cache is stale
        const isStale = this.isCacheStale();
        console.log('Cache stale check:', { dataLoaded: this.dataLoaded, isStale });
        
        if (!this.dataLoaded || isStale) {
            console.log('🔄 Loading fresh data from API...');
            await Promise.all([
                this.loadRequests(),
                this.loadFriends()
            ]);
            this.dataLoaded = true;
        } else {
            console.log('✅ Using cached data, no API calls needed');
        }
        
        console.log('Final data state:', { 
            friends: this.friends.length, 
            incoming: this.incoming.length, 
            outgoing: this.outgoing.length 
        });
    }
}
</script>

<style scoped>
.friends-panel{ width: 100%; }
.avatar{ width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.result-item, .request-item{ padding: 10px 12px; border-bottom: 1px solid #eee; border-radius: 10px; transition: background-color .2s ease, transform .1s ease; }
.result-item:hover, .request-item:hover{ background-color: #f9fafb; }
.result-item:last-child, .request-item:last-child{ border-bottom: 0; }
.bg-gradient-dark{ background: linear-gradient(135deg, #1f2937 0%, #374151 100%); }
.
.list-scroll{ max-height: 340px; overflow: auto; padding-right: 4px; }
.list-scroll::-webkit-scrollbar{ width: 6px; }
.list-scroll::-webkit-scrollbar-thumb{ background-color: rgba(0,0,0,0.15); border-radius: 4px; }

@media (max-width: 991.98px) {
  .friends-panel{ padding: 0; }
}
</style>


