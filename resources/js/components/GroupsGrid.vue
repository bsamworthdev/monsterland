<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row">
                        <button class="btn btn-info ml-3 mb-2" @click="openCreateGroupModal($event)">
                            <i class="fa fa-plus"></i> Create New
                        </button>
                    </div>
                    <div v-if="groups.length>0" class="row mb-3">
                        <div v-for="group in groups" class="monster col-lg-4 col-6" :key="group.id">
                            <group-item-component
                                :group="group">
                            </group-item-component>
                        </div>
                    </div>
                    <div v-else class="row">
                        <h3 class="pl-2"><i>You have not set up any private groups</i></h3>
                    </div>
                </div>

            </div>
        </div>
        <create-group-component
            v-if="activeModal==1" 
            @close="activeModal=0" >
        </create-group-component>
    </div>
</template>

<script>
    import groupItemComponent from './GroupItem' ;
    import createGroupComponent from './CreateGroup';

    export default {
        props: {
            groups: Array,
        },
        components: {
            groupItemComponent,
            createGroupComponent
        },
        methods: {
            openCreateGroupModal: function(){
                this.activeModal = 1;
            },
        },
        computed: {
            
        },
        data() {
            return {
                activeModal: 0
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>

</style>
