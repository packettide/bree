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
		$markup = "<template type='x-handlebars-template' id='".$this->name."-row'>" . $this->generateRow(get_class($row)) . "</template>" . $markup;
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

		// if($this->select === false)
		// {
		// 	return $options;
		// }

		// if($this->hasMultiple())
		// {
		// 	return '<select multiple name="'.$this->name.'[]" id="'.$this->name.'"'.$attrs.'>'. $options .'</select>';
		// }
		// else
		// {
		// 	return '<select name="'.$this->name.'" id="'.$this->name.'"'.$attrs.'>'. $options .'</select>';
		// }

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

		foreach ($newData as $related) {
			if (isset($related['id']))
			{
				$this->related->baseModel->find($related['id'])->update(array_except($related, 'id'));
				$newMember = $this->related->baseModel->find($related['id']);
			}
			else
			{
				$newMember = $this->related->create($related);
			}
			if ($newMember instanceof \Packettide\Bree\Model)
			{
				$newMember = $newMember->baseModel;
			}
			$this->relation->save($newMember);
		}

		$this->data['_'.$this->prefix.'delete'] = isset($this->data['_'.$this->prefix.'delete'])? $this->data['_'.$this->prefix.'delete'] : array();

		foreach ($this->data['_'.$this->prefix.'delete'] as $value) {
			if ($value != -1)
			{
				$this->related->baseModel->destroy($value);
			}
		}
	}


}