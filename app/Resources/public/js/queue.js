var Queue = function() {
    var urlPrefix = /^[a-z]+:\/\/[a-z0-9\.-]+\/app_dev\.php/.test(window.location) ? '/app_dev.php' : '';
    
    this._listeners = {all:[]};
    this._changeId = null;
    this._status = "disconnected";
    this._buffer = [];
    
    this._changesUrl = urlPrefix + '/api/changes/';
    this._loginUrl   = urlPrefix + '/login/';

    this._initSocket();
}

Queue.prototype = {
        _setStatus: function(status) {
            this._status = status;
            this._dispatch("connection", this._status);
        },
        _initSocket: function() {
            this._sock = new SockJS("http://" + window.location.hostname + ":5550");
            this._sock.onopen = $.proxy(this._onopen, this);
            this._sock.onmessage = $.proxy(this._onmessage, this);
            this._sock.onheartbeat = $.proxy(this._onheartbeat, this);
            this._setHeartbeatTimeout();
        },
        _setHeartbeatTimeout: function() {
            if (this._timeout) {
                clearTimeout(this._timeout);
            }
            this._timeout = setTimeout($.proxy(this._reconnect, this), 40000);
        },
        _sync: function() {
            this._setStatus("syncing");
            $.ajax({
                url: this._changesUrl + this.changeId,
                success: $.proxy(this._onSync, this)
            });
        },
        _onSync: function(data) {
            var changeId = null, found = false;
            for(var i = 0, l = data.length; i < l; i++) {
                changeId = data[i].id;
                this._dispatchMessage(data[i]);
            }
            //Search for new messages that could be recieved while we was waiting for sync response
            for (var i = 0, l = this._buffer.length; i < l; i++) {
                if (found) {
                    this._dispatchMessage(this._buffer[i]);
                    changeId = this._buffer[i].id;
                } else {
                    if (this._buffer[i].id == changeId) {
                        found = true;
                    }
                }
            }
            this._buffer = [];
            this._changeId = changeId;
            this._setStatus("connected");
        },
        _reconnect: function() {
            if (this.sock) {
                this.sock.close();
            }
            this._setStatus("disconnected");
            this._timeout = null;
            this._initSocket();
        },
        _onopen: function() {
            console.log('Web socket connection opened');
            //set status connecting, wait for incoming message or heartbeat
            this._setStatus("connecting");
        },
        _onmessage: function(e) {
            var data = $.parseJSON(e.data);
            console.log('Data recieved from web socket', data);
            this._setHeartbeatTimeout();
            
            if (this._status == "connecting") {
                this._setStatus("connected");
            }
            
            if (this._status == "connected") {
                if (data['last_change_id'] && this._changeId) {
                    if (this._changeId != data['last_change_id']) {
                        this._sync();
                    }
                }
                
                if (data['change_id']) {
                    this._changeId = data['changeId'];
                }

                if (data.error) {
                    if (data.error.code == 401) {
                        window.location.assign(this._loginUrl);
                    }
                    this._dispatch("error", data);
                } else {
                    this._dispatchMessage(data);
                }
            } else if (this._status == "syncing") {
                this._buffer.push(data);
            }
        },
        _onclose: function() {
            //try to reconnect
            console.log('Web socket closed trying to reconnect');
            this._initSocket();
        },
        _onheartbeat: function() {
            if (this._status == "connecting") {
                this._setStatus("connected");
            }
            this._sock.send('h');
            this._setHeartbeatTimeout();
        },
        on: function() {
            var args = arguments;
            if (args.length == 1 && $.isFunction(args[0])) {
                this._listeners["all"].push(args[0]);
            } else if (args.length == 2 && typeof args[0] == "string" && $.isFunction(args[1])) {
                var event = args[0], callback = args[1];
                if (!this._listeners[event]) {
                    this._listeners[event] = [];
                }
                this._listeners[event].push(callback);
            } else {
                throw "Parameters wrong";
            }
        },
        _dispatchMessage: function(data) {
            var _this = this;
            this._dispatch("message", data);
            $.each(data, function(index, value) {
                _this._dispatch("message:" + index, data);
            });
        },
        _dispatch: function(event) {
            var i, l, e = [];
            for (i = 0, l = this._listeners.all.length; i < l; i++) {
                try {
                    this._listeners.all[i].apply(null, arguments);
                } catch (ex) {
                    e.push(ex);
                }
            }
            if (this._listeners[event]) {
                var argsWithoutEvent = [];
                for (i = 1, l = arguments.length; i < l; i++) {
                    argsWithoutEvent.push(arguments[i]);
                }
                for (i = 0, l = this._listeners[event].length; i < l; i++) {
                    try {
                        this._listeners[event][i].apply(null, argsWithoutEvent);
                    } catch (ex) {
                        e.push(ex);
                    }
                }
            }
            if (e.length > 0) {
                throw e[0];
            }
        }
}

var queue = new Queue();