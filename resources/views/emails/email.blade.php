@extends('layouts.emaillayout')
@section("content")
<table class="table">
    <thead>
    <tr>
		<?php
		foreach ($data as $key => $value){
			if (isset($value['field']) || isset($value['value'])){
				echo 	'<th>Field name</th>
                    <th>Value</th>';
				break;
			}
		}
		?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $key => $value) {
    ?>
    <tr>
        <td><?php if(isset($value['field'])) echo $value['field']; ?></td>
        <td><?php if(isset($value['value']))  echo $value['value']; ?></td>
        <td><?php if(isset($value['smart_hub'])) echo $value['smart_hub'];?></td>
    </tr>
    <?php
    } ?>
    </tbody>
</table>
@stop