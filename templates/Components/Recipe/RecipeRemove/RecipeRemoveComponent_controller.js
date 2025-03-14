import ItemRemoveController from 'App/Templates/Components/HomeSection/ItemRemove/ItemRemoveComponent_controller';

export default class extends ItemRemoveController {
    connect() {
        super.connect();

        this.formRemoveItemIdFieldName = `${this.element.name}[recipes_id][]`;
    }
}