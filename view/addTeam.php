<h2>Add new team</h2>

<form method="POST" action="?action=addTeam">
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" placeholder="Name" name="name">
  </div>
  
  <div class="form-group">
    <label for="desc">Description</label>
    <textarea class="form-control" id="desc" name="desc" rows="3" placeholder="Team Description"></textarea>
  </div>
  <div class="radio">
    <label>
      <input type="radio" name="openjoin" id="optionsRadios1" value="1">
      Users may join this team
    </label>
  </div>
  <div class="radio">
    <label>
      <input type="radio" name="openjoin" id="optionsRadios1" value="0" checked>
      Users may <strong>NOT</strong> join this team
    </label>
  </div>
  <button type="submit" class="btn btn-success">Create Team</button>
</form>
