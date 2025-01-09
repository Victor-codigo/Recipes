import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// HOME LIST
import HomeSectionComponent from 'App/Twig/Components/HomeSection/Home/HomeSection_controller';
import HomeListComponent from 'App/Twig/Components/HomeSection/HomeList/List/HomeList_controller';
import HomeListItemComponent from 'App/Twig/Components/HomeSection/HomeList/ListItem/HomeListItem_controller';

// HOME LIST
app.register('HomeSectionComponent', HomeSectionComponent);
app.register('HomeListComponent', HomeListComponent);
app.register('HomeListItemComponent', HomeListItemComponent);