import { createStore, applyMiddleware } from 'redux'
import { persistStore, persistReducer } from 'redux-persist'
import storage from 'redux-persist/lib/storage'
import { composeWithDevTools } from "@redux-devtools/extension";
import reducer from './rootReducer'
import middleware from './middleware'

const persistConfig = {
    key: 'reducer',
    storage: storage,
    whitlist: ['login'],
}

const persistedReducer = persistReducer(persistConfig, reducer)

const composeEnhancers = composeWithDevTools({
   trace: true
});
const configStore = (initialState = {}) => {
    const store = createStore(
        persistedReducer,
        initialState,
        composeEnhancers(
            applyMiddleware(...middleware)
        )
    )
    return {
        persistor: persistStore(store),
        store
    }
}

const { store, persistor } = configStore();
global.store = store;

export { store, persistor };