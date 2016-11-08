function getUTCDateNow() {
    var dateTimeNow = new Date();
    return dateTimeNow.getUTCFullYear() + "-" + (dateTimeNow.getUTCMonth() + 1) + "-" + dateTimeNow.getUTCDate() + " " + dateTimeNow.getUTCHours() + ":" + dateTimeNow.getUTCMinutes() + ":" + dateTimeNow.getUTCSeconds();
}