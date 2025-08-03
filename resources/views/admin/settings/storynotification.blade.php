@extends('admin.layout.app')
<link href="{{url('assets/select/styles/multiselect.css')}}" rel="stylesheet"/>
<script src="{{url('assets/select/scripts/multiselect.min.js')}}"></script>
<style>
    #testSelect1_multiSelect {
        width: 100%;
    }
    .multiselect-wrapper .multiselect-list {
        padding: 5px;
        min-width: 91%;
    }
    #textPreview {
        padding: 10px;
        text-align: center;
        border: 1px solid #ccc;
        margin-bottom: 20px;
    }
</style>
@section ('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
            <div class="alert alert-success">
                @if(is_array(session()->get('success')))
                <ul>
                    @foreach (session()->get('success') as $message)
                    <li>{{ $message }}</li>
                    @endforeach
                </ul>
                @else
                {{ session()->get('success') }}
                @endif
            </div>
            @endif
            @if (count($errors) > 0)
            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                {{$errors->first()}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
            @endif
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">{{ __('keywords.Story to Users')}}</h4>
                    <form class="forms-sample" action="{{route('adminStoryStore')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                </div>
                <div class="card-body">
                    <div class="row">

                        <!-- Champ Type -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="typeSelect">{{ __('keywords.Type story')}}</label>
                                <select id="typeSelect" name="type" class="form-control" onchange="toggleFields(this.value)">
                                    <option value="text">{{ __('keywords.Select story')}}</option>
                                    <option value="text">{{ __('keywords.Text')}}</option>
                                    <option value="image">{{ __('keywords.Image')}}</option>
                                    <option value="video">{{ __('keywords.Video')}}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Input pour la couleur du texte (uniquement pour Text) -->
                        <div class="col-md-12" id="textColorInput" style="display: none;">
                            <div class="form-group">
                                <label>{{ __('keywords.Text Color')}}</label>
                                <input type="color" name="text_color" class="form-control" id="textColorPicker" value="#ffffff">
                            </div>
                        </div>

                        <!-- Input pour la couleur de fond (uniquement pour Text) -->
                        <div class="col-md-12" id="backgroundColorInput" style="display: none;">
                            <div class="form-group">
                                <label>{{ __('keywords.Background Color')}}</label>
                                <input type="color" name="background_color" class="form-control" id="backgroundColorPicker">
                            </div>
                        </div>

                        <!-- Champ pour l'image (uniquement pour Image) -->
                        <div class="col-md-12" id="imageInput" style="display: none;">
                            <label class="bmd-label-floating">{{ __('keywords.Image')}} ({{ __('keywords.1000 KB')}})</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="imageFile" name="image" accept="image/*"/>
                                <label class="custom-file-label" for="imageFile">Choose file</label>
                            </div>
                        </div>

                        <!-- Champ pour la vidéo (uniquement pour Video) -->
                        <div class="col-md-12" id="videoInput" style="display: none;">
                            <label class="bmd-label-floating">{{ __('keywords.Video')}} ({{ __('keywords.5000 KB')}})</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="videoFile" name="video" accept="video/*"/>
                                <label class="custom-file-label" for="videoFile">Choose file</label>
                            </div>
                        </div>

                        <!-- Message (affiché pour tous les types) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('keywords.Message')}}</label>
                                <textarea name="text" class="form-control" id="textMessage" oninput="updatePreview()"></textarea>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- Aperçu -->
                    <div id="textPreview" style="display: none;">Aperçu du texte</div>

                    <button type="submit" class="btn btn-primary pull-center">{{ __('keywords.Send Story to App Users')}}</button>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Fonction pour afficher/masquer les champs dynamiquement
    function toggleFields(type) {
        const backgroundColorInput = document.getElementById('backgroundColorInput');
        const textColorInput = document.getElementById('textColorInput');
        const imageInput = document.getElementById('imageInput');
        const videoInput = document.getElementById('videoInput');
        const textPreview = document.getElementById('textPreview');

        // Masquer tous les champs par défaut
        backgroundColorInput.style.display = 'none';
        textColorInput.style.display = 'none';
        imageInput.style.display = 'none';
        videoInput.style.display = 'none';
        textPreview.style.display = 'none';

        // Afficher les champs correspondants
        if (type === 'text') {
            backgroundColorInput.style.display = 'block';
            textColorInput.style.display = 'block';
            textPreview.style.display = 'block';
        } else if (type === 'image') {
            imageInput.style.display = 'block';
        } else if (type === 'video') {
            videoInput.style.display = 'block';
        }
    }

    // Fonction pour mettre à jour l'aperçu en temps réel
    function updatePreview() {
        const text = document.getElementById('textMessage').value;
        const backgroundColor = document.getElementById('backgroundColorPicker').value;
        const textColor = document.getElementById('textColorPicker').value;
        const textPreview = document.getElementById('textPreview');

        textPreview.textContent = text;
        textPreview.style.backgroundColor = backgroundColor;
        textPreview.style.color = textColor;
    }

    // Écouteurs pour mettre à jour les couleurs en temps réel
    document.getElementById('backgroundColorPicker').addEventListener('input', updatePreview);
    document.getElementById('textColorPicker').addEventListener('input', updatePreview);
</script>
@endsection
