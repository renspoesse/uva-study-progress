<template>

    <div class="app">

        <div class="container">
            <div class="row">

                <div class="col-sm-3">

                    <sidebar-nav></sidebar-nav>

                </div>
                <div class="col-sm-9">

                    <transition name="fade-fast" appear>
                        <router-view></router-view>
                    </transition>

                </div>

            </div>
        </div>

    </div>

</template>