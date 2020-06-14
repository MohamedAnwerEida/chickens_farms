export default class Gate {

    constructor(user) {
        this.user = user;
    }


    isAdmin() {
        return this.user.type === 'Admin';
    }

    isDataEntry() {
        return this.user.type === 'Data Entry';
    }

}