<div class="filter-range">
    <div class="range-slider">
        <input type="text" class="js-range-slider" value="" />
    </div>
    <div class="extra-controls form-inline">
        <div class="filter-price">
            <input type="text" name="filter_price_from" class="js-input-from input-default" value="0" /><span>-</span>
            <input type="text" name="filter_price_to" class="js-input-to input-default" value="0" />
            <input type="hidden" name="from_percent">
            <input type="hidden" name="to_percent">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.11/lodash.min.js" integrity="sha256-7/yoZS3548fXSRXqc/xYzjsmuW3sFKzuvOCHd06Pmps=" crossorigin="anonymous"></script>

<script>
// Trigger

$(function () {

  var $range = $(".js-range-slider"),
      $inputFrom = $(".js-input-from"),
      $inputTo = $(".js-input-to"),
      instance,
      min = {{$min}},
      max = {{$max}},
      from = {{$from}},
      to = {{$to}};

  $range.ionRangeSlider({
      type: "double",
      min: min,
      max: max,
      from: from,
      to: to,
      onStart: updateInputs,
      onChange: updateInputs,
      step: 1,
      postfix: ' грн',
      prettify_enabled: true,
      prettify_separator: " ",
      values_separator: " - ",
      force_edges: true,
      onFinish: function(data)
      {
        $("input[name='from_percent']").val(data.from_percent);
        $("input[name='to_percent']").val(data.to_percent);
        filterIt();
      }


  });

  instance = $range.data("ionRangeSlider");

    function updateInputs (data) {
        from = data.from;
        to = data.to;

        $inputFrom.prop("value", from);
        $inputTo.prop("value", to);
    }

  $inputFrom.on("input", function () {
      var val = $(this).prop("value");

      // validate
      if (val < min) {
          val = min;
      } else if (val > to) {
          val = to;
      }

      instance.update({
          from: val
      });
  });

  $inputTo.on("input", function () {
      var val = $(this).prop("value");

      // validate
      if (val < from) {
          val = from;
      } else if (val > max) {
          val = max;
      }

      instance.update({
          to: val
      });
  });

      });
</script>