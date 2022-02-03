class Ajax
{
    route(route)
    {
        this.action = route;

        return this;
    }

    form(id)
    {
        this.formData = new FormData;

        this.formContent = document.getElementById(id);

        if (this.formContent)
        {
            this.action = this.action ? this.action: $(this.formContent).attr('action');

            this.formData = new FormData(this.formContent);
        }

        this.formData.append('requestedWith', 'ajax');

        return this;
    }

    data(data = {})
    {
        data.requestedWith = 'ajax';

        this.formData = new FormData;

        for (const [key, value] of Object.entries(data))
        {
            this.formData.append(key, value);
        }

        return this;
    }

    condition(type)
    {
        this.conditionType = type;

        return this;
    }

    flush(area)
    {
        this.flushArea = area;

        return this;
    }

    container(area)
    {
        this.contentArea = area;

        return this;
    }

    loader(area, icon = null)
    {
        this.loaderArea = area;

        this.prev = $('#' + area).html();

        this.icon = icon ? icon : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>';

        return this;
    }

    send()
    {
        var ajax = this;

        $('#' + ajax.loaderArea).html(this.icon);

        this.request(function (data)
        {
            if (data.includes('flush:'))
            {
                data =  data.split('flush:').pop();

                ajax.contentArea = ajax.flushArea;

                ajax.conditionType = 'replace';
            }

            ajax.response(data, ajax);

            if (ajax.loaderArea != ajax.contentArea)
            {
                $('#' + ajax.loaderArea).html(ajax.prev);
            }
        });

    }

    request(callback)
    {
        return $.ajax({

            xhr: function()
            {
                var xhr = $.ajaxSettings.xhr();

                xhr.upload.onprogress = function(e)
                {
                    var percentComplete = Math.floor(e.loaded / e.total *100);

                    $('#upload_response').html(percentComplete + '% uploaded');

                    if (percentComplete == 100)
                    {
                        $('#upload_response').html('Please wait... saving document');
                    }
                };

                return xhr;
            },

            url: config().host + '/' + this.action,
            type: 'post',
            data: this.formData,
            contentType: false,
            processData: false,
            success: callback,
        });
    }

    response(data, ajax)
    {
        if (data.includes('location:'))
        {
            return window.location.href = config().host + data.split(':')[1];
        }
        console.log();

        if (ajax.conditionType === 'append')
        {
            return $('#' + ajax.contentArea).append(data);
        }

        if (ajax.conditionType === 'prepend')
        {
           return $('#' + ajax.contentArea).prepend(data);
        }

        $('#' + ajax.contentArea).html(data);
    }

}
