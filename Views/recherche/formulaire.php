<div role="main" id="main">
  <div class="page-header">
    <h1>Recherche avancée</h1>
  </div>
  <div class="well">
    <?php echo  validation_errors('<div class="alert alert-error">', '</div>'); ?>
    <table class="table table-striped table-bordered">
    <tbody>

      <tr>
        <th><label for="search-input">Catégorie</label></th>
        <td>
          <select name="search-input" id="search-input">
            <option>-- Veuillez choisir une option --</option>
            <option value="projet">Projet</option>
            <option value="utilisateur">Utilisateur</option>
            <option value="document">Document</option>
          </select>
        </td>
      </tr>

    </tbody>
    </table>

    <!-- Partie projet -->
    <form id="search-projet" action="<?php echo current_url(); ?>" method="post">
    <input type="hidden" id="categorie" name="categorie" value="projet" />
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th><label for="nom">Nom</label></th>
          <td><input type="text" name="nom" id="nom" /></td>
        </tr>
        <tr>
          <th><label for="statut">Statut</label></th>
          <td><input type="text" name="statut" id="statut" /></td>
        </tr>
        <tr>
          <th><label for="date_deb">Date début</label></th>
          <td><input type="text" name="date_deb" id="date_deb" /></td>
        </tr>
        <tr>
          <th><label for="date_fin">Date fin</label></th>
          <td><input type="text" name="date_fin" id="date_fin" /></td>
        </tr>
        <tr>
          <th><label for="tag">Tags</label></th>
          <td><input type="text" name="tag" id="tag" /></td>
        </tr>
        <tr>
          <th><label for="tri">Tri</label></th>
          <td>
            <select name="tri" id="tri">
              <option value="alphabetique">Alphabétique</option>
              <option value="date">Date</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="ordre">Ordre</label></th>
          <td>
            <select name="ordre" id="ordre">
              <option value="ASC">Croissant</option>
              <option value="DESC">Décroissant</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <button type="submit" class="btn">Rechercher</button>
    </form>

    <!-- Partie utilisateur -->
    <form id="search-utilisateur" action="<?php echo current_url(); ?>" method="post">
    <input type="hidden" id="categorie" name="categorie" value="utilisateur" />
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th><label for="nom">Nom Prénom</label></th>
          <td><input type="text" name="nom" id="nom" /></td>
        </tr>
        <tr>
          <th><label for="statut">Statut</label></th>
          <td><input type="text" name="statut" id="statut" /></td>
        </tr>
        <tr>
          <th><label for="date_deb">Date début</label></th>
          <td><input type="text" name="date_deb" id="date_deb" /></td>
        </tr>
        <tr>
          <th><label for="date_fin">Date fin</label></th>
          <td><input type="text" name="date_fin" id="date_fin" /></td>
        </tr>
        <tr>
          <th><label for="tri">Tri</label></th>
          <td>
            <select name="tri" id="tri">
              <option value="alphabetique">Alphabétique</option>
              <option value="date">Date</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="ordre">Ordre</label></th>
          <td>
            <select name="ordre" id="ordre">
              <option value="ASC">Croissant</option>
              <option value="DESC">Décroissant</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <button type="submit" class="btn">Rechercher</button>
    </form>

    <!-- Partie document -->
    <form id="search-document" action="<?php echo current_url(); ?>" method="post">
    <input type="hidden" id="categorie" name="categorie" value="document" />
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <th><label for="nom">Nom</label></th>
          <td><input type="text" name="nom" id="nom" /></td>
        </tr>
        <tr>
          <th><label for="proprietaire">Proprietaire</label></th>
          <td><input type="text" name="proprietaire" id="proprietaire" /></td>
        </tr>
        <tr>
          <th><label for="date_deb">Date début</label></th>
          <td><input type="text" name="date_deb" id="date_deb" /></td>
        </tr>
        <tr>
          <th><label for="date_fin">Date fin</label></th>
          <td><input type="text" name="date_fin" id="date_fin" /></td>
        </tr>
        <tr>
          <th><label for="date_deb_prise">Date début prise</label></th>
          <td><input type="text" name="date_deb_prise" id="date_deb_prise" /></td>
        </tr>
        <tr>
          <th><label for="date_fin_prise">Date fin prise</label></th>
          <td><input type="text" name="date_fin_prise" id="date_fin_prise" /></td>
        </tr>
        <tr>
          <th><label for="type">Type</label></th>
          <td><input type="text" name="type" id="type" /></td>
        </tr>
        <tr>
          <th><label for="tag">Tags</label></th>
          <td><input type="text" name="tag" id="tag" /></td>
        </tr>    
        <tr>
          <th><label for="tri">Tri</label></th>
          <td>
            <select name="tri" id="tri">
              <option value="alphabetique">Alphabétique</option>
              <option value="date">Date</option>
              <option value="type">Type</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="ordre">Ordre</label></th>
          <td>
            <select name="ordre" id="ordre">
              <option value="ASC">Croissant</option>
              <option value="DESC">Décroissant</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <button type="submit" class="btn">Rechercher</button>
    </form>

  </div>
</div>