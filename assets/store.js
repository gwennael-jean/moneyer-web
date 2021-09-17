import {createStore} from "vuex"

export default createStore({
    state: {
        accounts: []
    },
    getters: {
        getAccounts(state) {
            return state.accounts
        },
    },
    mutations: {
        addAccount(state, account) {
            state.accounts = [...state.accounts, account]
        },
        updateAccount(state, account) {
            state.accounts = state.accounts.map((item) => item.id == account.id ? account : item)
        },
        removeAccount(state, account) {
            state.accounts = state.accounts.filter((item) => item.id != account.id)
        }
    }
})
