<?php
$self->setHeaderTitle("Register"); ?>
<h1>Register</h1>
<?php
if(isset($errors) && !empty($errors)){
    echo '<div>';
    RHtmlHelper::showValidationErrors($errors);
    echo '</div>';
}
?>
<?=RFormHelper::openForm("user/register",array('class'=>'vform'))?>

<?=RFormHelper::label("Username","name")?>
<?=RFormHelper::input("name",isset($form["name"])?$form["name"]:"")?>

<br/>

<?=RFormHelper::label("Email","email")?>
<?=RFormHelper::input("email",isset($form["email"])?$form["email"]:"")?>

<br/>

<?=RFormHelper::label("Password","password")?>
<?=RFormHelper::input(array('type'=>"password","name"=>"password"),isset($form["password"])?$form["password"]:"")?>

<br/>

<?=RFormHelper::label("Password confirm","password-confirm")?>
<?=RFormHelper::input(array('type'=>"password","name"=>"password-confirm"),isset($form["password-confirm"])?$form["password-confirm"]:"")?>

<br/>

<button type="submit">Register</button>

<?=RFormHelper::endForm()?>
