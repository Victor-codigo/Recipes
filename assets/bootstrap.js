import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// ALERT COMPONENT
import AlertValidationComponent from 'App/Templates/Components/Alert/AlertComponent_controller.js';

// MODAL COMPONENT
import ModalComponent from 'App/Templates/Components/Modal/Modal_Component.js';

// DROP ZONE COMPONENT
import DropZoneComponent from 'App/Templates/Components/DropZone/DropZone_Component.js';

// PAGINATION COMPONENT
import PaginatorComponent from 'App/Templates/Components/Paginator/Paginator_Component.js';

// LIST COMPONENT
import ListComponent from 'App/Templates/Components/List/List_controller.js';

// HOME LIST COMPONENT
import HomeSectionComponent from 'App/Templates/Components/HomeSection/Home/HomeSection_controller';
import HomeListComponent from 'App/Templates/Components/HomeSection/HomeList/List/HomeList_controller';
import HomeListItemComponent from 'App/Templates/Components/HomeSection/HomeList/ListItem/HomeListItem_controller';

// RECIPE HOME COMPONENT
import RecipeSectionComponent from 'App/Templates/Components/Recipe/RecipeHome/Home/RecipeHomeSection_controller.js';
import RecipeListItemComponent from 'App/Templates/Components/Recipe/RecipeHome/ListItem/RecipeListItem_controller.js';
import RecipeCreateComponent from 'App/Templates/Components/Recipe/RecipeCreate/RecipeCreate_controller.js';
import RecipeItemAddComponent from 'App/Templates/Components/Recipe/RecipeItemAdd/RecipeItemAdd_controller.js';


// ALERT COMPONENT
app.register('AlertValidationComponent', AlertValidationComponent);

// MODAL COMPONENT
app.register('ModalComponent', ModalComponent);

// DROP ZONE COMPONENT
app.register('DropZoneComponent', DropZoneComponent);

// PAGINATION COMPONENT
app.register('PaginatorComponent', PaginatorComponent);

// LIST COMPONENT
app.register('ListComponent', ListComponent);

// HOME LIST COMPONENT
app.register('HomeSectionComponent', HomeSectionComponent);
app.register('HomeListComponent', HomeListComponent);
app.register('HomeListItemComponent', HomeListItemComponent);

// RECIPE HOME COMPONENT
app.register('RecipeSectionComponent', RecipeSectionComponent);
app.register('RecipeListItemComponent', RecipeListItemComponent);
app.register('RecipeCreateComponent', RecipeCreateComponent);
app.register('RecipeItemAddComponent', RecipeItemAddComponent);