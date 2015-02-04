<?php
$team = new team();
$teaminfo = $team->viewTeam($_GET['team']);
?>

<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li><a href="?action=viewTeams">Teams</a></li>
  <li>
  <?php echo "<a href='?action=viewTeam&team=$teaminfo->id'>$teaminfo->name</a>";?>
  </li>
  <li class="active">Add new team badge</li>
</ol>

<div class="page-header">
  <h1>
    <small>Add a new badge to team</small>
    <?php echo $teaminfo->name; ?>
  </h1>
</div>

<div class="row">
<div class="col-md-6">
  <div class="color-choices">
<div class="color-choice" data-color="#002B75" data-text="white" style="background:#002B75"></div>
<div class="color-choice" data-color="#0050D4" data-text="white" style="background:#0050D4"></div>
<div class="color-choice" data-color="#00D9F7" data-text="black" style="background:#00D9F7"></div>
<div class="color-choice" data-color="#00A6A6" data-text="white" style="background:#00A6A6"></div>
<div class="color-choice" data-color="#00B562" data-text="white" style="background:#00B562"></div>
<div class="color-choice" data-color="#00D942" data-text="black" style="background:#00D942"></div>
<div class="color-choice" data-color="#B4D900" data-text="black" style="background:#B4D900"></div>
<div class="color-choice" data-color="#EBCF00" data-text="black" style="background:#EBCF00"></div>
<div class="color-choice" data-color="#EB7700" data-text="white" style="background:#EB7700"></div>
<div class="color-choice" data-color="#EB0012" data-text="white" style="background:#EB0012"></div>
<div class="color-choice" data-color="#790009" data-text="white" style="background:#790009"></div>
<div class="color-choice" data-color="#FF00C3" data-text="white" style="background:#FF00C3"></div>
<div class="color-choice" data-color="#8D00FF" data-text="white" style="background:#8D00FF"></div>
<div class="color-choice" data-color="#ffffff" data-text="black" style="background:#ffffff"></div>
<div class="color-choice" data-color="#777777" data-text="white" style="background:#777777"></div>
<div class="color-choice" data-color="#333333" data-text="white" style="background:#333333"></div>
<div class="color-choice" data-color="#000000" data-text="white" style="background:#000000"></div>
  </div>

  <form method="POST"
    action="<?php echo "?action=addNewBadge&team=$teaminfo->id";?>
  ">
    <div class="form-group">
      <label for="name">Name</label>
      <input type="text" class="form-control" id="name" placeholder="Name" name="name">
    </div>
    
    <div class="form-group">
      <label for="desc">Description</label>
      <input type="text" class="form-control" id="desc"
      placeholder="Description" name="desc">
    </div>
    <div class="form-group">
      <label for="name">Color</label>
      <input type="text" class="form-control" id="color" placeholder=" Color" name="color">
    </div>
    <div class="form-group">
      <label for="name">Text Color</label>
      <input type="text" class="form-control" id="color2" placeholder=" Text Color" name="color2">
    </div>
    <div class="form-group">
      <label for="name">Icon</label>
      <input type="text" class="form-control" id="icon" placeholder="Icon" name="icon">
    </div>
    <button type="submit" class="btn btn-success">Create Badge</button>
  </form>
</div>
<div class="col-md-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Badge Preview</h3>
    </div>
    <div class="panel-body">
      <h3>
        <span class="label label-default" id="badge-preview">
          <i class="fa fa-shield" id="badge-icon"></i>
          <span id="badge-text">Badge Text</span>
        </span>
      </h3>
    </div>
  </div>
</div>
</div>

