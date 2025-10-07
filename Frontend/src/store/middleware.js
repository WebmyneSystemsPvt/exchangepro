import { thunk } from 'redux-thunk';
import createSagaMiddleware from 'redux-saga';

const middleware = [thunk];

export const sagaMiddleware = createSagaMiddleware();
// const invariant = require('redux-immutable-state-invariant').default;
middleware.push(sagaMiddleware);
if (process.env.NODE_ENV === `development`) {
    const { createLogger } = require('redux-logger');
    middleware.push(createLogger({ collapsed: true }))
}
export default middleware