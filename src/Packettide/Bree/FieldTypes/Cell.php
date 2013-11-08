<?php namespace Packettide\Bree\FieldTypes;

use Packettide\Bree\FieldTypeRelation;
use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Database\Eloquent\Relations;

class Cell extends FieldTypeRelation {

	private $prefix = "cell_";

	/**
	 *
	 */
	public function field($attributes = array())
	{
		$attrs = $this->getFieldAttributes($attributes);

		$available = $this->related->all();
		$chosen = $this->data;
		$options = '';


		if($available instanceof Collection)
		{
			foreach($available as $row)
			{
				if($chosen->contains($row->getKey()))
				{
					$options .= $this->generateRow($row);
				}
			}
		}

		$markup  = "<table $attrs><thead>" . $this->generateHeaders($row) . "</thead><tbody id='".$this->name."-body'>" . $options . "</tbody></table><a id='add-row-".$this->name."'>+ Add Row</a>";
		$markup = "<script type='x-handlebars-template' id='".$this->name."-row'>" . $this->generateRow(get_class($row)) . "</script>" . $markup;
		$markup = "<script>
			\$(function () {
				var source   = \$('#{$this->name}-row').html();
				var template = Handlebars.compile(source);
				\$('#add-row-{$this->name}').click(function () {
					\$('#{$this->name}-body').append(template());
					\$(this).trigger('bree.cell.add');
				});
				\$('body').on('click', '.delete-row-{$this->name}', function () {
					\$(this).parents('tr').hide();
					var id = \$(this).parents('tr').find('input[type=hidden]').val();
					\$(this).parents('form').append('<input type=\"hidden\" name=\"{$this->prefix}{$this->name}[_{$this->prefix}delete][]\" value=\"'+id+'\">')
					if(id === '-1') {
						\$(this).parents('tr').remove();
					}
					return false;
				});
			});
		</script>" . $markup;
		return $markup;

	}

	public function generateHeaders($row)
	{
		$toReturn = "<tr>";
		$admin = new \Packettide\Bree\Model($row);
		foreach ($admin->fields as $key => $value) {
			$toReturn .= '<th>';
			$toReturn .= $admin->$key->label();
			$toReturn .= '</th>';
		}
		$toReturn .= '<th>Delete</th>';
		return $toReturn . '</tr>';
	}

	public function generateRow($row)
	{
		$toReturn = "<tr>";
		if (is_object($row))
		{
			$toReturn .= "<input type='hidden' name=".$this->prefix.$this->name.'[id][]'." value='{$row->id}'>";
		}
		else
		{
			$toReturn .= "<input type='hidden' name=".$this->prefix.$this->name.'[id][]'." value='-1'>";
		}
		$admin = new \Packettide\Bree\Model($row);
		$admin->attachObserver("field.render", function($name, $data, $attrs) {
			return array($this->prefix.$this->name.'['.$name.'][]', $data, $attrs);
		});
		foreach ($admin->fields as $key => $value) {
			$toReturn .= '<td>';
			$toReturn .= $admin->$key->field();
			$toReturn .= '</td>';
		}
		$toReturn .= '<td><a href="#" class="delete-row-'.$this->name.'">X</td>';
		return $toReturn . '</tr>';
	}

	/**
	 *
	 */
	public function save()
	{
		if(empty($this->data)) return;


		var_dump($this->data);
		// First we need to clear out any relations that were marked for deletion
		$this->data['_'.$this->prefix.'delete'] = isset($this->data['_'.$this->prefix.'delete'])? $this->data['_'.$this->prefix.'delete'] : array();

		foreach ($this->data['_'.$this->prefix.'delete'] as $value) {
			var_dump($value);
			if ($value != -1)
			{
				$this->related->destroy($value);
			}
		}


		// Clean up the array structure
		$headLen = count(head(array_except($this->data, '_'.$this->prefix.'delete')));

		$newData = array();

		for ($i=0; $i < $headLen; $i++) {
			$newData[$i] = array();
			foreach (array_except($this->data, '_'.$this->prefix.'delete') as $key => $value) {
				if ($key != "id" || $value[$i] != -1)
				{
					$newData[$i][$key] = $value[$i];
				}
			}
		}

		foreach ($newData as $row) {
			// @todo - This could be cleaned up with a way to generate new instances from existing Bree\Model
			$rowModel = (isset($row['id'])) ? $this->related->find($row['id']) : new \Packettide\Bree\Model($this->related->baseModel);

			// We loop through each row and set the attributes
			// so that the Bree\Model can call save() on each field
			foreach(array_except($row, 'id') as $key => $value)
			{
				$rowModel->$key = $value;
			}

			$rowModel->save();

			if ($rowModel instanceof \Packettide\Bree\Model)
				$rowModel = $rowModel->baseModelInstance;

			$this->relation->save($rowModel);
		}

	}


}