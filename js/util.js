function getUTCDateNow() {
    var dateTimeNow = new Date();
    return dateTimeNow.getUTCFullYear() + "-" + dateTimeNow.getUTCMonth() + "-" + dateTimeNow.getUTCDate() + " " + dateTimeNow.getUTCHours() + ":" + dateTimeNow.getUTCMinutes() + ":" + dateTimeNow.getUTCSeconds();
}