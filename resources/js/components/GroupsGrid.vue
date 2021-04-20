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
                        <div v-for="group in requiredGroups" class="monster col-lg-4 col-md-6 col-12" :key="group.id">
                            <group-item-component
                                :group="group"
                                :user="user">
                            </group-item-component>
                        </div>
                    </div>
                    <div v-else class="row">
                        <h5 class="pt-4 pl-3"><i>No groups found</i></h5>
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
            user: Object
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
            requiredGroups:function(){
                if (this.user.id == 1){
                    var arr = [];
                    var group;
                    for(var i=0; i < this.groups.length; i++){
                        group = this.groups[i];
                        if (group.monsters.length > 0) {
                            arr.push(group);
                        }
                    }
                    return arr;
                } else {
                    return this.groups;
                }
            }
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
