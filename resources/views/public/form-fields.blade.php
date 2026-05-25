<div class="row g-3">
    <div class="col-md-6"><input class="form-control" name="first_name" placeholder="Prénom" required></div>
    <div class="col-md-6"><input class="form-control" name="last_name" placeholder="Nom" required></div>
    <div class="col-md-6"><input class="form-control" name="email" type="email" placeholder="Email" required></div>
    <div class="col-md-6"><input class="form-control" name="phone" placeholder="Téléphone" required></div>
    <div class="col-md-6"><input class="form-control" name="city" placeholder="Ville"></div>
    <div class="col-md-6"><input class="form-control" name="education_level" placeholder="Niveau scolaire"></div>

    <div class="col-12">
        <div class="documents-upload">
            <div class="documents-upload__head">
                <strong>Pièces à joindre au dossier</strong>
                <span>Formats acceptés : PDF, JPG, PNG - 4 Mo maximum par fichier.</span>
            </div>

            <label>
                <span>Copie CIN / carte nationale <small>obligatoire pour finaliser le dossier</small></span>
                <input class="form-control" type="file" name="documents[cin]" accept=".pdf,.jpg,.jpeg,.png">
            </label>

            <label>
                <span>Diplôme, certificat de scolarité ou attestation de niveau <small>selon la filière choisie</small></span>
                <input class="form-control" type="file" name="documents[diplome]" accept=".pdf,.jpg,.jpeg,.png">
            </label>

            <label>
                <span>Relevés de notes <small>si disponibles</small></span>
                <input class="form-control" type="file" name="documents[releves]" accept=".pdf,.jpg,.jpeg,.png">
            </label>

            <label>
                <span>Photo d’identité <small>optionnelle à la préinscription</small></span>
                <input class="form-control" type="file" name="documents[photo]" accept=".jpg,.jpeg,.png">
            </label>

            <p>Si vous n’avez pas encore toutes les pièces, envoyez la préinscription maintenant. L’administration EPIM vous contactera pour compléter le dossier.</p>
        </div>
    </div>

    <div class="col-12"><textarea class="form-control" name="message" rows="3" placeholder="Message"></textarea></div>
</div>
