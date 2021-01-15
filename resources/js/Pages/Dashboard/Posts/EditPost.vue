<template>
    <Dashboard>
        <template #breadcrumb> / ویرایش خبر / {{post.title}}</template>
        <template #dashboardContent>
            <div>
                <div class="bg-gray-300  rounded-lg">
                    <div class="mt-5 md:mt-0">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6 my-2">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="md:col-span-6">
                                        <jet-label for="title" value="عنوان"/>
                                        <jet-input name="title" ref="title"
                                                   v-model="postForm.title"
                                                   class="w-full mt-3"/>
                                    </div>
                                    <div class="md:col-span-4">
                                        <jet-label for="post_category_id" value="دسته بندی"/>
                                        <select name="post_category_id"
                                                class="form-input rounded-md shadow-sm w-full mt-3 pr-6"
                                                id="post_category_id"
                                                rel="post_category_id"
                                                v-model="postForm.post_category_id">
                                            <option v-for="category in categories" :key="category.id"
                                                    :value="category.id">
                                                {{category.name}}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <jet-label for="status" value="وضعیت نمایش"/>
                                        <jet-button class="mt-3 bg-green-200 hover:bg-green-600 active:bg-green-600"
                                                    :class="{'bg-green-500':postForm.status===1}"
                                                    @click.native="postForm.status=1">
                                            انتشار
                                        </jet-button>
                                        <jet-button class="mt-3 bg-red-200 hover:bg-red-600 active:bg-red-600"
                                                    :class="{'bg-red-500':postForm.status===0}"
                                                    @click.native="postForm.status=0">
                                            عدم انتشار
                                        </jet-button>
                                    </div>
                                    <div class="md:col-span-6">
                                        <jet-label value="گروه های کاربری"/>
                                        <jet-secondary-button class="mx-2 mt-3"
                                                              :class="{'bg-blue-400 text-gray-500':postForm.levels.indexOf(level.id)!==-1}"
                                                              v-for="level in levels" :key="level.id"
                                                              @click.native="addLevel(level.id)">
                                            {{level.name}}
                                        </jet-secondary-button>
                                    </div>
                                    <div class="md:col-span-6">
                                        <jet-label for="body" value="متن خبر"/>
                                        <editor
                                            v-model="postForm.body"
                                            api-key="mrw1bys0ou0vwpb1p3614ugc2lt2s8aglyajfvtchddcfy2y"
                                            :init="editorOptions"
                                        />
                                    </div>
                                    <div class="md:col-span-6">
                                        <jet-section-border></jet-section-border>
                                    </div>
                                    <div class="md:col-span-2">
                                        <jet-label for="body" value="ارسال فایل"/>
                                        <input type="file" multiple class="hidden" ref="uploadFilesInput"
                                               @change="onUploadFilesChange">
                                        <jet-button type="button" class="mt-3" @click.native="selectFiles">انتخاب فایل ها</jet-button>
                                    </div>
                                    <div class="md:col-span-4">
                                        <p class="block border-gray-300 border-b py-1 text-left"
                                           v-for="(file,index) in post.files"
                                           :key="index">
                                            {{file.name}} -
                                            <i class="material-icons text-red-500 cursor-pointer"
                                               v-on:click="removeUploadedFile(file)">remove_circle</i></p>
                                        <p class="block border-gray-300 border-b py-1 text-left"
                                           v-for="(file,index) in selectedFiles"
                                           :key="index">
                                            {{file.name}} - {{Math.round(file.size / 1024)}} KB -
                                            <i class="material-icons text-red-500 cursor-pointer"
                                               v-on:click="removeUploadFile(file)">remove_circle</i></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <jet-label for="body" value="ارسال ویدیو"/>
                                        <input type="file" multiple class="hidden" ref="uploadVideosInput"
                                               @change="onUploadVideosChange">
                                        <jet-button type="button" class="mt-3" @click.native="selectVideos">انتخاب ویدیو ها</jet-button>
                                    </div>
                                    <div class="md:col-span-4">
                                        <p class="block border-gray-300 border-b py-1 text-left"
                                           v-for="(video,index) in post.videos"
                                           :key="index">
                                            {{video.name}} -
                                            <i class="material-icons text-red-500 cursor-pointer"
                                               v-on:click="removeUploadedVideo(video)">remove_circle</i></p>
                                        <p class="block border-gray-300 border-b py-1 text-left"
                                           v-for="(video,index) in selectedVideos"
                                           :key="index">
                                            {{video.name}} - {{Math.round(video.size / 1024)}} KB -
                                            <i class="material-icons text-red-500 cursor-pointer"
                                               v-on:click="removeUploadVideo(video)">remove_circle</i></p>
                                    </div>
                                    <div class="md:col-span-6 text-left">
                                        <jet-button @click.native="submitPost">ارسال</jet-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from "@/Jetstream/Button";
    import JetSecondaryButton from "@/Jetstream/SecondaryButton";
    import JetLabel from "@/Jetstream/Label";
    import JetInputError from "@/Jetstream/InputError";
    import JetInput from "@/Jetstream/Input";
    import JetSectionBorder from "@/Jetstream/SectionBorder";
    import Editor from '@tinymce/tinymce-vue';

    export default {
        name: "EditPost",
        components: {
            Dashboard,
            JetButton,
            JetSecondaryButton,
            JetInputError,
            JetInput,
            JetLabel,
            'editor': Editor,
            JetSectionBorder
        },
        props: {
            post: Object,
            categories: Array,
            levels: Array,
        },
        data() {
            return {
                selectedFiles: [],
                selectedVideos: [],
                postForm: this.$inertia.form({
                    '_method': 'PUT',
                    post_category_id: this.post.post_category_id,
                    title: this.post.title,
                    body: this.post.body,
                    levels: [],
                    status: this.post.status,
                    uploadFiles: [],
                    deleteFiles: [],
                    uploadVideos: [],
                    deleteVideos: [],
                    user_id: this.post.user_id
                }, {
                    bag: 'postForm',
                    resetOnSuccess: false
                }),
                editorOptions: {
                    height: 500,
                    plugins: '' +
                        'print preview paste importcss searchreplace autolink autosave save ' +
                        'directionality code visualblocks visualchars fullscreen image link media ' +
                        'template codesample table charmap hr pagebreak nonbreaking anchor toc ' +
                        'insertdatetime advlist lists wordcount imagetools textpattern noneditable ' +
                        'help charmap quickbars emoticons',
                    images_upload_url: '/api/dashboard/contents/upload',
                    menubar: 'file edit view insert format tools table tc help',
                    toolbar: 'undo redo | ' +
                        'bold italic underline strikethrough | ' +
                        'fontselect fontsizeselect formatselect | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'outdent indent |  ' +
                        'numlist bullist | ' +
                        'forecolor backcolor removeformat | ' +
                        'image media pageembed template link anchor codesample | ' +
                        'charmap emoticons | ' +
                        'fullscreen  preview save print | ' +
                        'ltr rtl',
                    font_formats: 'Iran Sanse=IRANSans;B Titr=BTitr;B Nazanin=BNazanin;B Yekan=BYekan;',
                    content_css: '/css/app.css'
                }
            }
        },
        mounted() {
            this.setLevels();
        },
        methods: {
            setLevels() {
                let levels = this.post.levels;
                for (let i = 0; i < levels.length; i++) {
                    this.postForm.levels.push(levels[i].level);
                }
            },
            addLevel(id) {
                let levels = this.postForm.levels;
                let index = levels.indexOf(id);
                if (index !== -1) {
                    levels.splice(index, 1);
                } else {
                    levels.push(id);
                }
                this.postForm.levels = levels;
            },
            selectFiles() {
                this.$refs.uploadFilesInput.click();
            },
            onUploadFilesChange(e) {
                const files = e.target.files;
                for (let i = 0; i < files.length; i++) {
                    this.selectedFiles.push(files[i]);
                }
            },
            removeUploadFile(file) {
                this.selectedFiles = this.selectedFiles.filter(f => {
                    return f !== file;
                });
            },
            removeUploadedFile(file) {
                this.postForm.deleteFiles.push(file.id);
                this.post.files = this.post.files.filter(f => {
                    return f.id !== file.id
                });
            },
            selectVideos() {
                this.$refs.uploadVideosInput.click();
            },
            onUploadVideosChange(e) {
                const videos = e.target.files;
                for (let i = 0; i < videos.length; i++) {
                    this.selectedVideos.push(videos[i]);
                }
            },
            removeUploadVideo(file) {
                this.selectedVideos = this.selectedVideos.filter(f => {
                    return f !== file;
                });
            },
            removeUploadedVideo(file) {
                this.postForm.deleteVideos.push(file.id);
                this.post.videos = this.post.videos.filter(f => {
                    return f.id !== file.id
                });
            },
            submitPost() {
                this.postForm.uploadFiles = this.selectedFiles;
                this.postForm.uploadVideos = this.selectedVideos;

                this.postForm.post(route('dashboard.posts.update', {postId: this.post.id})).then(response => {

                })
            }
        }
    }
</script>

<style scoped>

</style>
